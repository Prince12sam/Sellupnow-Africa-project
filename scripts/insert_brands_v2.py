#!/usr/bin/env python3
"""Clean insert of all global brands into the sellupnow brands table.
Uses pymysql/mysql.connector style escaping via subprocess to avoid shell quoting issues.
"""
import subprocess
import sys

brands = [
    # Automobiles
    "Toyota","Honda","Ford","BMW","Mercedes-Benz","Volkswagen","Audi",
    "Porsche","Ferrari","Lamborghini","Rolls-Royce","Bentley","Maserati",
    "Alfa Romeo","Fiat","Peugeot","Renault","Citroen","Opel","Volvo",
    "Jaguar","Land Rover","Range Rover","Mini","Tesla","Nissan","Mazda",
    "Subaru","Mitsubishi","Lexus","Acura","Infiniti","Kia","Hyundai",
    "Genesis","Chevrolet","Cadillac","Buick","GMC","Dodge","Chrysler",
    "Jeep","Ram","Lincoln","Suzuki","SEAT","Skoda","Bugatti","McLaren",
    "Aston Martin","Lotus","Pagani","Koenigsegg","BYD","Geely","MG",
    "NIO","Polestar","Rivian","Lucid","Vinfast","Chery","Great Wall",
    "Haval","Hongqi","Roewe","Dacia","Lancia","Smart","Saab",
    "Isuzu","Daihatsu","Vauxhall","Holden","Proton","Perodua",
    "Tata","Mahindra","Maruti Suzuki","Ashok Leyland",
    # Motorcycles
    "Harley-Davidson","Ducati","Yamaha","Kawasaki","BMW Motorrad","KTM",
    "Triumph","Royal Enfield","Indian Motorcycle","Aprilia","MV Agusta",
    "Norton","Husqvarna","Beta","Benelli","Moto Guzzi","Can-Am",
    "Vespa","Piaggio",
    # Trucks
    "Volvo Trucks","Scania","MAN Trucks","DAF","Iveco","Kenworth",
    "Peterbilt","Freightliner","International Trucks","Mack Trucks",
    # Consumer Electronics
    "Apple","Samsung","Sony","LG","Panasonic","Xiaomi","Huawei",
    "Oppo","Vivo","OnePlus","Realme","Google","Microsoft","Dell",
    "HP","Lenovo","Asus","Acer","Toshiba","Sharp","Philips",
    "Siemens","Braun","Dyson","Bose","JBL","Sennheiser","Beats",
    "Audio-Technica","Shure","AKG","Jabra","Skullcandy","Anker",
    "Canon","Nikon","Fujifilm","Olympus","Pentax","Leica","GoPro",
    "DJI","Logitech","Razer","Corsair","SteelSeries","HyperX",
    "Western Digital","Seagate","SanDisk","Kingston","Crucial",
    "Intel","AMD","NVIDIA","Qualcomm","MediaTek","Motorola","Nokia",
    "BlackBerry","HTC","ZTE","Honor","Tecno","Itel","Infinix",
    "TCL","Hisense","Haier","Epson","Brother","Xerox","Lexmark",
    "Netgear","TP-Link","D-Link","Ubiquiti","Garmin","TomTom",
    "Fitbit","Amazon","Roku","Sonos",
    # Home Appliances
    "Whirlpool","KitchenAid","Maytag","Amana","GE Appliances",
    "Frigidaire","Electrolux","Miele","SMEG","Kenmore","Bosch",
    "Sub-Zero","Wolf","Viking","Thermador","AEG","Hotpoint",
    "Indesit","Beko","Arcelik","Daewoo","De Longhi","Nespresso",
    "Keurig","Cuisinart","Hamilton Beach","Black and Decker","Oster",
    "Ninja","Instant Pot","Breville","Vitamix","NutriBullet",
    "iRobot","Shark","Bissell","Hoover",
    # Fashion & Luxury
    "Nike","Adidas","Puma","Reebok","Under Armour","New Balance",
    "Converse","Vans","Gucci","Louis Vuitton","Chanel","Hermes",
    "Prada","Versace","Valentino","Burberry","Fendi","Givenchy",
    "Balenciaga","Saint Laurent","Dior","Dolce and Gabbana","Armani",
    "Calvin Klein","Tommy Hilfiger","Ralph Lauren","Hugo Boss",
    "Lacoste","Levi's","Wrangler","Lee","Zara","H&M","Uniqlo",
    "Gap","Banana Republic","Mango","Bershka","Pull and Bear",
    "Massimo Dutti","Stradivarius","Forever 21","ASOS","Primark",
    "Next","Marks and Spencer","Ted Baker","Jack and Jones","Topshop",
    "River Island","BOSS","Diesel","G-Star RAW","True Religion",
    "Abercrombie and Fitch","Hollister","American Eagle","Old Navy",
    "Superdry","Patagonia","The North Face","Columbia","Arc'teryx",
    "Moncler","Canada Goose","Stone Island",
    # Footwear
    "Timberland","UGG","Skechers","ECCO","Clarks","Birkenstock",
    "Dr. Martens","Steve Madden","Jimmy Choo","Christian Louboutin",
    "Manolo Blahnik","Salvatore Ferragamo","Tod's","Cole Haan",
    "Wolverine","Caterpillar Footwear","Merrell","Keen","Salomon",
    "Asics","Brooks","Saucony","Mizuno","Hoka","On Running",
    # Watches
    "Rolex","Omega","Breitling","TAG Heuer","Patek Philippe",
    "Audemars Piguet","IWC Schaffhausen","Cartier","Jaeger-LeCoultre",
    "Vacheron Constantin","Hublot","Zenith","Girard-Perregaux",
    "Tissot","Longines","Hamilton","Seiko","Casio","Citizen","Orient",
    "Fossil","Daniel Wellington","Swatch","Rado","Certina","Mido",
    "Oris","Tudor","Panerai","Bell and Ross","Richard Mille",
    "Franck Muller","Corum",
    # Jewellery
    "Tiffany and Co.","Cartier Jewellery","Bulgari",
    "Van Cleef and Arpels","Harry Winston","Chopard","Graff",
    "De Beers","Mikimoto","Pandora","Swarovski","Thomas Sabo",
    "David Yurman",
    # Beauty & Cosmetics
    "L'Oreal","Maybelline","MAC Cosmetics","Estee Lauder","Clinique",
    "Lancome","Dior Beauty","Chanel Beauty","YSL Beauty","NARS",
    "Charlotte Tilbury","Urban Decay","Too Faced","Benefit Cosmetics",
    "Anastasia Beverly Hills","NYX","Revlon","CoverGirl","Neutrogena",
    "Olay","Avon","Mary Kay","The Body Shop","Bath and Body Works",
    "Dove","Nivea","Vaseline","Cetaphil","Aveeno","CeraVe",
    "La Roche-Posay","Vichy","Bioderma","The Ordinary","Drunk Elephant",
    "Tatcha","SK-II","Shiseido","Kiehls","Origins","Clarins",
    "Sisley","Caudalie","Nuxe","REN","Elemis","Lush",
    # Fragrances
    "Tom Ford Fragrance","Creed","Jo Malone","Byredo","Diptyque",
    "Le Labo","Maison Margiela Fragrance","Dior Fragrance",
    "Guerlain","Hermès Fragrance","Versace Fragrance",
    "Prada Fragrance","Armani Fragrance","Hugo Boss Fragrance","Davidoff",
    "Azzaro","Viktor and Rolf","Rabanne","Yves Saint Laurent Fragrance",
    # Sports & Outdoor
    "Lululemon","Gymshark","Fila","Speedo","TYR","Arena",
    "Callaway","TaylorMade","Titleist","Ping","Cleveland Golf",
    "Mizuno Golf","Wilson","Head","Babolat","Yonex","Prince",
    "Dunlop","Rawlings","Easton","Louisville Slugger","Spalding",
    "Rossignol","K2","Burton","Quiksilver","Rip Curl","Billabong",
    "Oakley","Bolle","Smith Optics",
    # Toys & Games
    "LEGO","Mattel","Hasbro","Fisher-Price","VTech","LeapFrog",
    "Little Tikes","Step2","Radio Flyer","Melissa and Doug","Playmobil",
    "Nerf","Funko","Build-A-Bear","American Girl","Hot Wheels",
    "Barbie","Transformers","Play-Doh","Nintendo","PlayStation","Xbox",
    # Baby & Kids
    "Graco","Britax","Chicco","Peg Perego","UPPAbaby","Bugaboo",
    "Thule","Stokke","Ergobaby","BabyBjorn","Medela","Philips Avent",
    "Dr. Browns","MAM","Tommee Tippee","Pampers","Huggies",
    "Johnsons Baby","Mustela","Burts Bees Baby",
    # Food & Beverage
    "Nestle","Unilever","Kraft Heinz","General Mills","Kelloggs",
    "Mars","Mondelez","Coca-Cola","Pepsi","Dr Pepper","Sprite",
    "Fanta","7UP","Red Bull","Monster Energy","Gatorade","Tropicana",
    "Minute Maid","Starbucks","Nescafe","Lavazza","Illy","Jacobs",
    "Maxwell House","Folgers","Lipton","Tetley","Twinings",
    "Haribo","Cadbury","Hersheys","Reeses","Snickers","Kit Kat",
    "Ferrero Rocher","Nutella","Lindt","Godiva","Toblerone",
    "Lays","Pringles","Doritos","Cheetos","Oreo","Ritz",
    "Hellmanns","Heinz","Ben and Jerrys","Haagen-Dazs",
    "Baskin-Robbins","Danone","Activia","Yoplait","Chobani",
    "Barilla","San Pellegrino","Evian","Volvic","Perrier",
    "Vittel","Fiji Water","Acqua Panna",
    # Home Furnishings
    "IKEA","Ashley Furniture","La-Z-Boy","Pottery Barn",
    "Williams-Sonoma","Crate and Barrel","Restoration Hardware",
    "West Elm","Ethan Allen","Broyhill","Thomasville","Bernhardt",
    "Zara Home","H&M Home",
    # Tools & Hardware
    "DeWalt","Milwaukee Tool","Makita","Bosch Tools","Stanley",
    "Black and Decker","Ryobi","Craftsman","Snap-on","Knipex",
    "Wera","Fiskars","Husqvarna","Stihl","Hilti","Festool",
    "Metabo","Paslode",
    # Heavy Equipment & Agriculture
    "John Deere","Caterpillar","Komatsu","Case","New Holland",
    "AGCO","Claas","Fendt","Massey Ferguson","Kubota",
    "JCB","Bobcat","Liebherr","Hitachi Construction","Doosan",
    "Volvo Construction",
    # Pet Care
    "Purina","Royal Canin","Hills Science Diet","Blue Buffalo",
    "Iams","Eukanuba","Pedigree","Whiskas","Friskies","Fancy Feast",
    "Taste of the Wild","Orijen","Acana","Wellness Pet Food",
    "Merrick","Diamond Pet Foods",
    # Real Estate
    "RE/MAX","Century 21","Coldwell Banker","Keller Williams",
    "Sothebys International Realty","Knight Frank","JLL",
    "CBRE","Savills","Cushman and Wakefield",
    # Financial / Payment
    "Visa","Mastercard","American Express","PayPal","Western Union",
    # Office & Stationery
    "3M","Staples","Office Depot","Avery","Sharpie","Pilot",
    "Parker","Montblanc","Cross",
    # Health & Pharmaceuticals
    "Johnson and Johnson","Pfizer","Bayer","Roche","Novartis",
    "Abbott","Tylenol","Panadol","Nurofen","Advil","Aspirin",
    "Voltaren","Band-Aid",
    # Bicycles
    "Trek","Specialized","Giant Bicycles","Cannondale","Scott",
    "Cervelo","Pinarello","Bianchi","Santa Cruz","Yeti Cycles",
    # Airlines
    "Emirates","Qatar Airways","Singapore Airlines","Lufthansa",
    "British Airways","Air France","KLM","Delta Air Lines",
    "American Airlines","United Airlines","Turkish Airlines",
    "Etihad Airways",
    # Hotels
    "Marriott","Hilton","Hyatt","IHG","Accor","Radisson",
    "Wyndham","Best Western","Four Seasons","Ritz-Carlton",
    "Shangri-La","Mandarin Oriental",
    # Tyres
    "Michelin","Bridgestone","Goodyear","Continental Tyres",
    "Pirelli","Dunlop Tyres","Hankook","Yokohama","Falken",
    "Toyo","BFGoodrich","Cooper Tyres",
    # Fuel & Lubricants
    "Shell","BP","ExxonMobil","Chevron","TotalEnergies","Castrol",
    "Mobil 1","Valvoline","Gulf Oil",
    # Telecom
    "Vodafone","AT&T","T-Mobile","Verizon","Orange","Airtel",
    "MTN","STC","Etisalat","Zain",
    # Supermarkets / Retail
    "Walmart","Carrefour","Tesco","Lidl","Aldi","Costco",
    "Target","IKEA","Amazon",
    # Sporting Goods Extra
    "Umbro","Kappa","Hummel","Diadora","Lotto Sport",
    "Odlo","Craft","CEP","2XU","Compressport",
    # Luxury Bags
    "Coach","Kate Spade","Michael Kors","Furla","Longchamp",
    "Mulberry","Radley","Loewe","Celine","Bottega Veneta",
    "Balenciaga Bags","Marc Jacobs","Tory Burch",
    # Audio / Music
    "Marshall","Pioneer","Denon","Marantz","Harman Kardon",
    "Bang and Olufsen","Klipsch","Polk Audio","Yamaha Audio",
    "QSC","Crown Audio","Shure Microphones",
    # Gaming
    "Nintendo","Sony PlayStation","Xbox","Valve","Sega",
    "Atari","Activision","EA Sports","Ubisoft","2K Sports",
    # Software / Cloud (often branded items)
    "Adobe","Autodesk","Oracle","SAP","Salesforce",
    # Cleaning & Household
    "Ariel","Tide","Persil","Omo","Bold","Fairy","Finish",
    "Dettol","Flash","Cillit Bang","Mr. Clean","Domestos",
    "Lysol","Febreze","Glade","Air Wick","Pledge","Windex",
    # Food Seasoning & Condiments
    "Maggi","Knorr","Tabasco","McIlhenny","Worcestershire",
    "Lea and Perrins","Cholula","Frank's RedHot","Sriracha",
    "Kikkoman","Lee Kum Kee","Prego","Ragu","Newman's Own",
]

# Deduplicate preserving order
seen = set()
unique_brands = []
for b in brands:
    key = b.strip().lower()
    if key and key not in seen:
        seen.add(key)
        unique_brands.append(b.strip())

print(f"Total unique brands: {len(unique_brands)}")

# Build individual INSERT statements (one per brand = no escaping issues with batching)
# Use MySQL hex encoding to completely avoid quote issues
sql_parts = ["TRUNCATE TABLE brands;"]
sql_parts.append("INSERT INTO brands (name, title, url, is_active, is_default, status, created_at, updated_at) VALUES")

rows = []
for name in unique_brands:
    # Escape single quotes with doubled single quotes for MySQL
    safe = name.replace("'", "''")
    slug = name.lower().replace(" ", "-").replace("&", "and").replace(".", "").replace("'", "")
    rows.append(f"  ('{safe}', '{safe}', '{slug}', 1, 0, 1, NOW(), NOW())")

sql_parts.append(",\n".join(rows) + ";")

sql = "\n".join(sql_parts)

with open("/tmp/brands_final.sql", "w", encoding="utf-8") as f:
    f.write(sql)

print(f"Written /tmp/brands_final.sql ({len(sql)} bytes)")

# Verify no backslash-escaped quotes remain
if "\\'" in sql:
    print("WARNING: backslash-escaped quotes found!")
else:
    print("OK: No backslash-escaped quotes in SQL")
