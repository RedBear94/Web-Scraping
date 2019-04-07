from urllib.request import urlopen
page = urlopen("https://yandex.ru")
content = page.read()
content[840:1280]

str(content, "utf-8")[837:1270]

import requests
response = requests.get("https://yandex.ru")
response.encoding
response.text[837:1270]