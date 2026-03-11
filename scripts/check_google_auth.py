import subprocess
r = subprocess.run(
    ['mysql','-uadmin','-pnU7Ak80lRYUO4QkqPfxw','admin','--skip-column-names','-e',
     "SELECT option_name, LEFT(option_value,80) FROM static_options WHERE option_name IN ('google_client_id','google_client_secret','google_callback_url','enable_google_login','register_page_social_login_show_hide');"],
    capture_output=True, text=True
)
print(r.stdout)
if r.stderr:
    for line in r.stderr.splitlines():
        if 'Warning' not in line and line.strip():
            print('ERR:', line)
