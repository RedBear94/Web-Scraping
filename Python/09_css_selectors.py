from lxml import html
import requests
page_html = requests.get("https://ru.wikipedia.org/wiki/Физическая_величина").text
tree = html.fromstring(page_html)

table = tree.cssselect("table.wikitable")

table[0].xpath("tbody/tr[1]/th[2]/text()")[0].strip()