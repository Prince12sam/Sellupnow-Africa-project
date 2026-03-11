import subprocess
r = subprocess.run(
    ['mysql','-uadmin','-pnU7Ak80lRYUO4QkqPfxw','admin','--skip-column-names','-e',
     "SELECT option_name,option_value FROM static_options WHERE option_name IN ('site_google_captcha_enable','captcha_provider');"],
    capture_output=True, text=True
)
print(r.stdout)
if r.stderr:
    for line in r.stderr.splitlines():
        if 'Warning' not in line and line.strip():
            print('ERR:', line)
