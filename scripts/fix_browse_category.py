path = "/home/sellupnow/htdocs/www.sellupnow.com/main-file/listocean/core/plugins/PageBuilder/Addons/BrowseCategory/BrowseCategoryOne.php"
correct_line = "                ->whereNull('parent_id')\n"
with open(path) as f:
    lines = f.readlines()
fixed = []
for line in lines:
    if "whereNull" in line:
        fixed.append(correct_line)
    else:
        fixed.append(line)
with open(path, "w") as f:
    f.writelines(fixed)
print("Done")
with open(path) as f:
    for i, line in enumerate(f):
        if "whereNull" in line or ("status" in line and "Category" not in line):
            print(i, repr(line.rstrip()))
