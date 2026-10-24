#!/usr/bin/env bash
# Build the release package.
#
#   tools/build-release-zip.sh [ref|WORKTREE] [output-dir]
#
# Archive root: the coremio/ folder, ready to unpack over a WISECP 5 installation, plus
# the README files, CHANGELOG and LICENSE. Only tracked files are packed, so OS and
# editor files (__MACOSX, ._*, .DS_Store) can never get in; WORKTREE packs the working
# tree instead (tracked + untracked, minus ignored), for local testing.
#
# Writes DNASSL-WiseCP5-v<version>.zip and an unversioned copy, so
# releases/latest/download/DNASSL-WiseCP5.zip is a stable link.
set -euo pipefail

TREE="coremio"
LABEL="WiseCP5"

cd "$(dirname "$0")/.."
REF="${1:-HEAD}"
OUT_DIR="${2:-dist}"

if [ "$REF" = WORKTREE ]; then
    VERSION="$(tr -d '[:space:]' < VERSION)"
else
    VERSION="$(git show "$REF:VERSION" | tr -d '[:space:]')"
fi
[ -n "$VERSION" ] || { echo "VERSION not found" >&2; exit 1; }

WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

if [ "$REF" = WORKTREE ]; then
    git ls-files -co --exclude-standard -- "$TREE" 'README*.md' CHANGELOG.md LICENSE | while IFS= read -r f; do
        [ -f "$f" ] || continue
        mkdir -p "$WORK/$(dirname "$f")"
        cp "$f" "$WORK/$f"
    done
else
    git archive "$REF" "$TREE" | tar -x -C "$WORK"
    for f in $(git ls-tree --name-only "$REF" | grep -E '^(README.*\.md|CHANGELOG\.md|LICENSE)$' || true); do
        git show "$REF:$f" > "$WORK/$f"
    done
fi

mkdir -p "$OUT_DIR"
OUT_DIR="$(cd "$OUT_DIR" && pwd)"
VERSIONED="$OUT_DIR/DNASSL-${LABEL}-v${VERSION}.zip"
LATEST="$OUT_DIR/DNASSL-${LABEL}.zip"
rm -f "$VERSIONED" "$LATEST"
(cd "$WORK" && zip -q -r -X "$VERSIONED" .)
cp "$VERSIONED" "$LATEST"

echo "$VERSIONED"
echo "$LATEST"
