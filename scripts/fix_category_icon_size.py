css_path = '/home/sellupnow/htdocs/www.sellupnow.com/main-file/listocean/core/public/assets/frontend/css/main-style.css'

with open(css_path, 'r') as f:
    content = f.read()

# Narrow the card container left and right by adding max-width + centering
old_card = """.new-style .exploreCategories .singleCategories {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  padding: 10px;
  border: 1px solid;
  border-radius: 8px;
  background-repeat: no-repeat;
}"""

new_card = """.new-style .exploreCategories .singleCategories {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  padding: 10px;
  border: 1px solid;
  border-radius: 8px;
  background-repeat: no-repeat;
  max-width: 110px;
  margin: 0 auto;
}"""

if old_card in content:
    import shutil
    shutil.copy(css_path, css_path + '.bak')
    content = content.replace(old_card, new_card)
    with open(css_path, 'w') as f:
        f.write(content)
    print('SUCCESS: singleCategories max-width set to 110px')
else:
    print('ERROR: card pattern not found in CSS file')
    # Debug: show what's around line 10655
    lines = content.split('\n')
    for i, line in enumerate(lines[10650:10675], start=10651):
        print(f'{i}: {repr(line)}')
