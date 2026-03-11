#!/usr/bin/env python3
"""Remove duplicate brands keeping only the lowest id for each title."""
import subprocess

def run(sql):
    r = subprocess.run(
        ['mysql', '-u', 'admin', '-pnU7Ak80lRYUO4QkqPfxw', 'admin', '-e', sql],
        capture_output=True, text=True
    )
    if r.stdout.strip():
        print(r.stdout.strip())
    if r.stderr.strip() and 'Warning' not in r.stderr:
        print("ERR:", r.stderr.strip())
    return r

# Show duplicates
print("=== Duplicates ===")
run("SELECT title, COUNT(*) as cnt FROM brands GROUP BY title HAVING cnt > 1 ORDER BY cnt DESC LIMIT 30;")

# Count before
print("\n=== Count before ===")
run("SELECT COUNT(*) as total FROM brands;")

# Delete duplicates - keep the row with the lowest id for each title
run("""
DELETE b1 FROM brands b1
INNER JOIN brands b2
WHERE b1.title = b2.title AND b1.id > b2.id;
""")

# Count after
print("\n=== Count after ===")
run("SELECT COUNT(*) as total FROM brands;")

# Confirm no dupes remain
print("\n=== Remaining duplicates (should be empty) ===")
run("SELECT title, COUNT(*) as cnt FROM brands GROUP BY title HAVING cnt > 1 ORDER BY cnt DESC LIMIT 10;")

print("\nDone.")
