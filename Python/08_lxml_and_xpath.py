from lxml import html
import requests
page_html = requests.get("http://localhost:8080/planets.html").text

tree = html.fromstring(page_html)

[tr for  tr in tree.xpath("/html/body/div/table/tr")]

from lxml import etree

[etree.tostring(data)[:50] for data in tree.xpath("/html/body/div/table/tr")]

[etree.tostring(data) for data in tree.xpath("/html/body/div[3]/div[3]/div[4]/div/table[@class='wikitable']/caption")]
mass = tree.xpath("/html/body/div[3]/div[3]/div[4]/div/table[@class='wikitable']/caption/text()")[0].strip()
mass
