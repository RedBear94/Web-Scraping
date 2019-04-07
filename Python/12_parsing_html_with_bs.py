import requests
from bs4 import BeautifulSoup
html = requests.get("https://ru.wikipedia.org/wiki/Физическая_величина").text
soup = BeautifulSoup(html, "lxml")

str(soup)[:1000]

soup.html.body.div.next_sibling.next_sibling.next_sibling.next_sibling 
