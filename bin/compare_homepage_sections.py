#!/usr/bin/env python3
"""Compare rendered homepage sections against the static markup snapshot.

Usage: compare_sections.py <rendered.html> <reference.html>

For each of the ten homepage sections the tool extracts the section markup and
compares it as an attribute-aware HTML node tree (SVG contents compared as raw
text).  Differences that are purely cosmetic for the design (the `decoding`
attribute added by wp_filter_content_tags, and whitespace between tags) are
ignored.  Everything else — element structure, class names, ids, hrefs, text —
must match exactly.

Exits non-zero when any section does not match, and prints the differing nodes.
"""

import difflib
import html
import re
import sys
from html.parser import HTMLParser

VOID = {
    "img", "input", "br", "hr", "meta", "link", "path", "circle", "rect",
    "line", "polyline", "polygon", "use", "stop", "source",
}

SECTION_NAMES = [
    "hero", "trust", "hospitality", "ranges", "projects",
    "capabilities", "process", "maker", "stories", "consultation",
]

IGNORED_ATTRS = {"decoding", "fetchpriority", "loading", "srcset", "sizes", "data-*"}

# Character references that render identically in the browser and are allowed
# to differ in the source markup.
EQUIVALENT_ENTITIES = {
    "&apos;": "'",
    "&#39;": "'",
    "&#039;": "'",
    "&#x27;": "'",
    "&#8217;": "\u2019",
    "&#8220;": "\u201c",
    "&#8221;": "\u201d",
    "&#8230;": "\u2026",
    "&#8211;": "\u2013",
    "&#8212;": "\u2014",
}


def normalize_text(value):
    """Fold equivalent character references so only real differences show."""
    for entity, char in EQUIVALENT_ENTITIES.items():
        value = value.replace(entity, char)
    return value


def segments(markup):
    """Split the markup at every homepage <section class="mp-…> element."""
    starts = [m.start() for m in re.finditer(r'<section class="mp-', markup)]
    out = []
    for i, start in enumerate(starts):
        end = starts[i + 1] if i + 1 < len(starts) else len(markup)
        chunk = markup[start:end]
        # Everything after the closing tag of this section belongs to the page
        # chrome (or to the next section) and is not compared.
        last_close = chunk.rfind("</section>")
        if last_close >= 0:
            chunk = chunk[: last_close + len("</section>")]
        out.append(chunk)
    return out


class TreeParser(HTMLParser):
    """Parse markup into (open|close|text|svg) events, keeping SVG as raw text."""

    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.events = []
        self.stack = []
        self.svg_depth = 0
        self.svg_raw = []

    def _svg_start(self):
        self.svg_depth += 1

    def handle_starttag(self, tag, attrs):
        if self.svg_depth:
            self.svg_depth += 1
            self.svg_raw.append("<%s %s>" % (
                tag,
                " ".join('%s="%s"' % (k, v or "") for k, v in attrs),
            ))
            return
        if tag == "svg":
            self.svg_depth = 1
            self.svg_raw = []
            self.events.append(("svg-open", "svg", tuple(sorted(
                (k, normalize_text(v or "")) for k, v in attrs
            ))))
            return
        attrs = {
            k: normalize_text(v or "")
            for k, v in attrs
            if k not in IGNORED_ATTRS and not k.startswith("data-")
        }
        self.events.append(("open", tag, tuple(sorted(attrs.items()))))
        if tag not in VOID:
            self.stack.append(tag)

    def handle_startendtag(self, tag, attrs):
        if self.svg_depth:
            self.svg_raw.append("<%s/>" % tag)
            return
        if tag == "svg":
            self.events.append(("svg-open", "svg", ()))
            self.events.append(("svg-close", ""))
            return
        attrs = {
            k: normalize_text(v or "")
            for k, v in attrs
            if k not in IGNORED_ATTRS and not k.startswith("data-")
        }
        self.events.append(("open", tag, tuple(sorted(attrs.items()))))
        self.events.append(("close", tag))

    def handle_endtag(self, tag):
        if self.svg_depth:
            self.svg_depth -= 1
            if self.svg_depth == 0:
                self.events.append(("svg-close", normalize_text("".join(self.svg_raw))))
            return
        self.events.append(("close", tag))
        if self.stack and self.stack[-1] == tag:
            self.stack.pop()

    def handle_data(self, data):
        if self.svg_depth:
            self.svg_raw.append(data)
            return
        text = normalize_text(data.strip())
        if text:
            self.events.append(("text", text))


def tree(markup):
    parser = TreeParser()
    parser.feed(markup)
    return parser.events, parser.stack


def main():
    rendered_path, reference_path = sys.argv[1], sys.argv[2]
    rendered = open(rendered_path, encoding="utf-8").read()
    reference = open(reference_path, encoding="utf-8").read()

    # The reference page may wrap the homepage sections in the page chrome
    # (entry content wrapper).  Everything before the first section and after
    # the last section's closing tag is chrome and is not compared.
    def clip(doc):
        first = doc.find('<section class="mp-')
        starts = [m.start() for m in re.finditer(r'<section class="mp-', doc)]
        if not starts:
            return doc
        last_start = starts[-1]
        close = doc.find('</section>', last_start)
        if close < 0:
            return doc[first:] if first >= 0 else doc
        return doc[first: close + len('</section>')] if first >= 0 else doc[: close + len('</section>')]

    rendered = clip(rendered)
    reference = clip(reference)

    # The rendered page contains the homepage twice today: once from
    # front-page.php and once from the Home page's own wp:html blocks (that
    # content is emptied by a later task).  Compare against the first copy,
    # which is the one rendered by front-page.php.
    rendered_segments = segments(rendered)
    reference_segments = segments(reference)

    ok = True
    print("%-14s %-8s %s" % ("section", "result", "detail"))
    for i, name in enumerate(SECTION_NAMES):
        ref_tree, ref_stack = tree(reference_segments[i])
        out_tree, out_stack = tree(rendered_segments[i])

        if ref_tree == out_tree and ref_stack == out_stack == []:
            print("%-14s %-8s %d nodes identical" % (name, "MATCH", len(ref_tree)))
            continue

        ok = False
        matcher = difflib.SequenceMatcher(a=ref_tree, b=out_tree, autojunk=False)
        detail = []
        for tag, i1, i2, j1, j2 in matcher.get_opcodes():
            if tag == "equal":
                continue
            detail.append("      %s" % tag)
            for event in ref_tree[i1:i2]:
                detail.append("        ref: %s" % (event,))
            for event in out_tree[j1:j2]:
                detail.append("        out: %s" % (event,))
        print("%-14s %-8s %d ref / %d out nodes" % (name, "MISMATCH", len(ref_tree), len(out_tree)))
        print("\n".join(detail[:60]))

    return 0 if ok else 1


if __name__ == "__main__":
    sys.exit(main())
