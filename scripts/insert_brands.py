#!/usr/bin/env python3
"""Insert global brands into the sellupnow brands table."""

import subprocess

brands = [
    # ── AUTOMOBILES ───────────────────────────────────────────────────────────
    "Toyota", "Honda", "Ford", "BMW", "Mercedes-Benz", "Volkswagen", "Audi",
    "Porsche", "Ferrari", "Lamborghini", "Rolls-Royce", "Bentley", "Maserati",
    "Alfa Romeo", "Fiat", "Peugeot", "Renault", "Citroën", "Opel", "Volvo",
    "Jaguar", "Land Rover", "Range Rover", "Mini", "Tesla", "Nissan", "Mazda",
    "Subaru", "Mitsubishi", "Lexus", "Acura", "Infiniti", "Kia", "Hyundai",
    "Genesis", "Chevrolet", "Cadillac", "Buick", "GMC", "Dodge", "Chrysler",
    "Jeep", "Ram", "Lincoln", "Suzuki", "SEAT", "Skoda", "Bugatti", "McLaren",
    "Aston Martin", "Lotus", "Pagani", "Koenigsegg", "BYD", "Geely", "MG",
    "NIO", "Polestar", "Rivian", "Lucid", "Vinfast", "Chery", "Great Wall",
    "Haval", "Hongqi", "Roewe", "Dacia", "Lancia", "Smart", "Saab", "Pontiac",
    "Oldsmobile", "Hummer", "Saturn", "Mercury", "Isuzu", "Daihatsu",
    "Vauxhall", "Holden", "Proton", "Perodua", "Tata", "Mahindra", "Maruti",
    "Bajaj Auto", "Ashok Leyland",

    # ── MOTORCYCLES ────────────────────────────────────────────────────────────
    "Harley-Davidson", "Ducati", "Yamaha", "Kawasaki", "BMW Motorrad", "KTM",
    "Triumph", "Royal Enfield", "Indian Motorcycle", "Aprilia", "MV Agusta",
    "Norton", "Husqvarna", "Beta", "Benelli", "Moto Guzzi", "Can-Am",
    "Vespa", "Piaggio",

    # ── TRUCKS & COMMERCIAL ────────────────────────────────────────────────────
    "Volvo Trucks", "Scania", "MAN Trucks", "DAF", "Iveco", "Kenworth",
    "Peterbilt", "Freightliner", "International", "Mack Trucks",

    # ── CONSUMER ELECTRONICS & TECH ───────────────────────────────────────────
    "Apple", "Samsung", "Sony", "LG", "Panasonic", "Xiaomi", "Huawei",
    "Oppo", "Vivo", "OnePlus", "Realme", "Google", "Microsoft", "Dell",
    "HP", "Lenovo", "Asus", "Acer", "Toshiba", "Sharp", "Philips",
    "Siemens", "Braun", "Dyson", "Bose", "JBL", "Sennheiser", "Beats",
    "Audio-Technica", "Shure", "AKG", "Jabra", "Skullcandy", "Anker",
    "Canon", "Nikon", "Fujifilm", "Olympus", "Pentax", "Leica", "GoPro",
    "DJI", "Logitech", "Razer", "Corsair", "SteelSeries", "HyperX",
    "Western Digital", "Seagate", "SanDisk", "Kingston", "Crucial",
    "Intel", "AMD", "NVIDIA", "Qualcomm", "MediaTek", "Motorola", "Nokia",
    "BlackBerry", "HTC", "ZTE", "Honor", "Tecno", "Itel", "Infinix",
    "TCL", "Hisense", "Haier", "Epson", "Brother", "Xerox", "Lexmark",
    "Netgear", "TP-Link", "D-Link", "Asus Networking", "Ubiquiti",
    "Garmin", "TomTom", "Fitbit", "Amazon", "Roku", "Sonos",

    # ── HOME APPLIANCES ────────────────────────────────────────────────────────
    "Whirlpool", "KitchenAid", "Maytag", "Amana", "GE Appliances",
    "Frigidaire", "Electrolux", "Miele", "SMEG", "Kenmore", "Bosch",
    "Sub-Zero", "Wolf", "Viking", "Thermador", "AEG", "Hotpoint",
    "Indesit", "Beko", "Arçelik", "Daewoo", "De'Longhi", "Nespresso",
    "Keurig", "Cuisinart", "Hamilton Beach", "Black+Decker", "Oster",
    "Ninja", "Instant Pot", "Breville", "Vitamix", "NutriBullet",
    "iRobot", "Shark", "Bissell", "Hoover", "Miele Vacuum", "Dyson Vacuum",

    # ── FASHION & LUXURY ──────────────────────────────────────────────────────
    "Nike", "Adidas", "Puma", "Reebok", "Under Armour", "New Balance",
    "Converse", "Vans", "Gucci", "Louis Vuitton", "Chanel", "Hermès",
    "Prada", "Versace", "Valentino", "Burberry", "Fendi", "Givenchy",
    "Balenciaga", "Saint Laurent", "Dior", "Dolce & Gabbana", "Armani",
    "Calvin Klein", "Tommy Hilfiger", "Ralph Lauren", "Hugo Boss",
    "Lacoste", "Levi's", "Wrangler", "Lee", "Zara", "H&M", "Uniqlo",
    "Gap", "Banana Republic", "Mango", "Bershka", "Pull&Bear",
    "Massimo Dutti", "Stradivarius", "Forever 21", "ASOS", "Primark",
    "Next", "Marks & Spencer", "Ted Baker", "Jack & Jones", "Topshop",
    "River Island", "BOSS", "Diesel", "G-Star RAW", "True Religion",
    "Abercrombie & Fitch", "Hollister", "American Eagle", "Old Navy",
    "Superdry", "Patagonia", "The North Face", "Columbia", "Arc'teryx",
    "Moncler", "Canada Goose", "Stone Island",

    # ── FOOTWEAR ──────────────────────────────────────────────────────────────
    "Timberland", "UGG", "Skechers", "ECCO", "Clarks", "Birkenstock",
    "Dr. Martens", "Steve Madden", "Jimmy Choo", "Christian Louboutin",
    "Manolo Blahnik", "Salvatore Ferragamo", "Tod's", "Cole Haan",
    "Wolverine", "Caterpillar Footwear", "Merrell", "Keen", "Salomon",
    "Asics", "Brooks", "Saucony", "Mizuno", "Hoka", "On Running",

    # ── WATCHES ───────────────────────────────────────────────────────────────
    "Rolex", "Omega", "Breitling", "TAG Heuer", "Patek Philippe",
    "Audemars Piguet", "IWC Schaffhausen", "Cartier", "Jaeger-LeCoultre",
    "Vacheron Constantin", "A. Lange & Söhne", "Hublot", "Zenith",
    "Girard-Perregaux", "Tissot", "Longines", "Hamilton", "Seiko",
    "Casio", "Citizen", "Orient", "Fossil", "Michael Kors Watch",
    "Daniel Wellington", "Swatch", "Rado", "Certina", "Mido",
    "Frederique Constant", "Oris", "Tudor", "Panerai", "Bell & Ross",
    "Richard Mille", "Franck Muller", "Corum",

    # ── JEWELLERY ─────────────────────────────────────────────────────────────
    "Tiffany & Co.", "Cartier Jewellery", "Bulgari", "Van Cleef & Arpels",
    "Harry Winston", "Chopard", "Graff", "De Beers", "Mikimoto",
    "Pandora", "Swarovski", "Thomas Sabo", "David Yurman",

    # ── BEAUTY & COSMETICS ────────────────────────────────────────────────────
    "L'Oréal", "Maybelline", "MAC Cosmetics", "Estée Lauder", "Clinique",
    "Lancôme", "Dior Beauty", "Chanel Beauty", "YSL Beauty", "NARS",
    "Charlotte Tilbury", "Urban Decay", "Too Faced", "Benefit Cosmetics",
    "Anastasia Beverly Hills", "NYX", "Revlon", "CoverGirl", "Neutrogena",
    "Olay", "Avon", "Mary Kay", "The Body Shop", "Bath & Body Works",
    "Dove", "Nivea", "Vaseline", "Cetaphil", "Aveeno", "CeraVe",
    "La Roche-Posay", "Vichy", "Bioderma", "The Ordinary", "Drunk Elephant",
    "Tatcha", "SK-II", "Shiseido", "Kiehl's", "Origins", "Clarins",
    "Sisley", "Caudalie", "Nuxe", "REN", "Elemis", "Lush",

    # ── FRAGRANCES / PERFUMES ─────────────────────────────────────────────────
    "Tom Ford Fragrance", "Creed", "Jo Malone", "Byredo", "Diptyque",
    "Le Labo", "Maison Margiela Fragrance", "Dior Fragrance",
    "Guerlain", "Chanel Fragrance", "Hermès Fragrance", "Versace Fragrance",
    "Prada Fragrance", "Armani Fragrance", "Hugo Boss Fragrance",
    "Burberry Fragrance", "Calvin Klein Fragrance", "Davidoff", "Azzaro",
    "Yves Saint Laurent Fragrance", "Viktor & Rolf", "Rabanne",

    # ── SPORTS & OUTDOOR ──────────────────────────────────────────────────────
    "Lululemon", "Gymshark", "Fila", "Speedo", "TYR", "Arena",
    "Callaway", "TaylorMade", "Titleist", "Ping", "Cleveland Golf",
    "Mizuno Golf", "Wilson", "Head", "Babolat", "Yonex", "Prince",
    "Dunlop", "Rawlings", "Easton", "Louisville Slugger", "Spalding",
    "Rossignol", "K2", "Burton", "Quiksilver", "Rip Curl", "Billabong",
    "Oakley", "Bollé", "Smith Optics",

    # ── TOYS & GAMES ──────────────────────────────────────────────────────────
    "LEGO", "Mattel", "Hasbro", "Fisher-Price", "VTech", "LeapFrog",
    "Little Tikes", "Step2", "Radio Flyer", "Melissa & Doug", "Playmobil",
    "Nerf", "Funko", "Build-A-Bear", "American Girl", "Hot Wheels",
    "Barbie", "Transformers", "Play-Doh", "Monopoly", "Nintendo",
    "PlayStation", "Xbox",

    # ── BABY & KIDS ───────────────────────────────────────────────────────────
    "Graco", "Britax", "Chicco", "Peg Perego", "UPPAbaby", "Bugaboo",
    "Thule", "Stokke", "Ergobaby", "BabyBjörn", "Medela", "Philips Avent",
    "Dr. Brown's", "MAM", "Tommee Tippee", "Pampers", "Huggies",
    "Johnson's Baby", "Mustela", "Burt's Bees Baby",

    # ── FOOD & BEVERAGE ───────────────────────────────────────────────────────
    "Nestlé", "Unilever", "Kraft Heinz", "General Mills", "Kellogg's",
    "Mars", "Mondelez", "Coca-Cola", "Pepsi", "Dr Pepper", "Sprite",
    "Fanta", "7UP", "Red Bull", "Monster Energy", "Gatorade", "Tropicana",
    "Minute Maid", "Starbucks", "Nescafé", "Lavazza", "Illy", "Jacobs",
    "Maxwell House", "Folgers", "Lipton", "Tetley", "Twinings",
    "Haribo", "Cadbury", "Hershey's", "Reese's", "Snickers", "Kit Kat",
    "Ferrero Rocher", "Nutella", "Lindt", "Godiva", "Toblerone",
    "Lay's", "Pringles", "Doritos", "Cheetos", "Oreo", "Ritz",
    "Hellmann's", "Heinz", "Ben & Jerry's", "Häagen-Dazs",
    "Baskin-Robbins", "Danone", "Activia", "Yoplait", "Chobani",
    "Président", "Philadelphia Cream Cheese", "Laughing Cow",
    "Barilla", "San Pellegrino", "Evian", "Volvic", "Perrier",
    "Vittel", "Fiji Water", "Acqua Panna",

    # ── HOME FURNISHINGS & DÉCOR ──────────────────────────────────────────────
    "IKEA", "Ashley Furniture", "La-Z-Boy", "Pottery Barn",
    "Williams-Sonoma", "Crate & Barrel", "Restoration Hardware",
    "West Elm", "Ethan Allen", "Broyhill", "Thomasville", "Bernhardt",
    "Zara Home", "H&M Home",

    # ── TOOLS & HARDWARE ──────────────────────────────────────────────────────
    "DeWalt", "Milwaukee Tool", "Makita", "Bosch Tools", "Stanley",
    "Black & Decker", "Ryobi", "Craftsman", "Snap-on", "Knipex",
    "Wera", "Fiskars", "Husqvarna", "Stihl", "Hilti", "Festool",
    "Metabo", "Paslode",

    # ── HEAVY EQUIPMENT & AGRICULTURE ────────────────────────────────────────
    "John Deere", "Caterpillar", "Komatsu", "Case", "New Holland",
    "AGCO", "Claas", "Fendt", "Massey Ferguson", "Kubota",
    "JCB", "Bobcat", "Liebherr", "Hitachi Construction", "Doosan",
    "Volvo Construction",

    # ── PET CARE ──────────────────────────────────────────────────────────────
    "Purina", "Royal Canin", "Hill's Science Diet", "Blue Buffalo",
    "Iams", "Eukanuba", "Pedigree", "Whiskas", "Friskies", "Fancy Feast",
    "Taste of the Wild", "Orijen", "Acana", "Wellness Pet Food",
    "Merrick", "Diamond Pet Foods",

    # ── REAL ESTATE & PROPERTY ────────────────────────────────────────────────
    "RE/MAX", "Century 21", "Coldwell Banker", "Keller Williams",
    "Sotheby's International Realty", "Knight Frank", "JLL",
    "CBRE", "Savills", "Cushman & Wakefield",

    # ── BANKING & FINANCIAL ───────────────────────────────────────────────────
    "Visa", "Mastercard", "American Express", "PayPal", "Western Union",

    # ── OFFICE & BUSINESS ─────────────────────────────────────────────────────
    "3M", "Staples", "Office Depot", "Avery", "Sharpie", "Pilot",
    "Parker", "Montblanc", "Cross",

    # ── HEALTH & PHARMACEUTICALS ──────────────────────────────────────────────
    "Johnson & Johnson", "Pfizer", "Bayer", "Roche", "Novartis",
    "Abbott", "Tylenol", "Panadol", "Nurofen", "Advil", "Aspirin",
    "Voltaren", "Complan", "Ensure", "Pedialyte", "Band-Aid",

    # ── SPORTS EQUIPMENT (ADDITIONAL) ────────────────────────────────────────
    "Trek", "Specialized", "Giant Bicycles", "Cannondale", "Scott",
    "Cervélo", "Pinarello", "Bianchi", "Santa Cruz", "Yeti Cycles",

    # ── AIRLINES / TRAVEL ─────────────────────────────────────────────────────
    "Emirates", "Qatar Airways", "Singapore Airlines", "Lufthansa",
    "British Airways", "Air France", "KLM", "Delta Air Lines",
    "American Airlines", "United Airlines", "Turkish Airlines",
    "Etihad Airways",

    # ── HOTEL & HOSPITALITY ───────────────────────────────────────────────────
    "Marriott", "Hilton", "Hyatt", "IHG", "Accor", "Radisson",
    "Wyndham", "Best Western", "Four Seasons", "Ritz-Carlton",
    "Shangri-La", "Mandarin Oriental",

    # ── TYRES ─────────────────────────────────────────────────────────────────
    "Michelin", "Bridgestone", "Goodyear", "Continental Tyres",
    "Pirelli", "Dunlop Tyres", "Hankook", "Yokohama", "Falken",
    "Toyo", "BF Goodrich", "Cooper Tyres",

    # ── FUEL & ENERGY ─────────────────────────────────────────────────────────
    "Shell", "BP", "ExxonMobil", "Chevron", "Total Energies", "Castrol",
    "Mobil 1", "Valvoline", "Gulf Oil",
]

# Deduplicate while preserving order
seen = set()
unique_brands = []
for b in brands:
    if b.lower() not in seen:
        seen.add(b.lower())
        unique_brands.append(b)

print(f"Total unique brands: {len(unique_brands)}")

# Build SQL
rows = []
for name in unique_brands:
    escaped = name.replace("'", "''")
    rows.append(f"('{escaped}', 1, 0, 1, NOW(), NOW())")

chunk_size = 100
sql_chunks = []
for i in range(0, len(rows), chunk_size):
    chunk = rows[i:i+chunk_size]
    sql = ("INSERT INTO brands (name, is_active, is_default, status, created_at, updated_at) VALUES\n"
           + ",\n".join(chunk) + ";")
    sql_chunks.append(sql)

# Write to file
with open("/tmp/brands_insert.sql", "w") as f:
    for chunk in sql_chunks:
        f.write(chunk + "\n")

print(f"SQL written to /tmp/brands_insert.sql ({len(sql_chunks)} chunks)")
