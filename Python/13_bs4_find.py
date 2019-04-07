import requests
from bs4 import BeautifulSoup
html = requests.get("https://ru.wikipedia.org/wiki/Физическая_величина").text
soup = BeautifulSoup(html, "lxml")

table = soup.find("table")
str(table)[:100]

[str(tr)[:50] for tr in table.findAll("tr")]

table = soup.find("table", {"class": "wikitable"})

items = dict()
magnitude_rows = table.findAll("tr")
for i in magnitude_rows:
    tds = i.findAll("td")
    if tds:
        items[tds[0].text.strip()] = tds[3].text.strip()

str(items)