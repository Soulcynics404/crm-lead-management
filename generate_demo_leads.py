import csv
import random

first_names = ["James", "Mary", "Robert", "Patricia", "John", "Jennifer", "Michael", "Linda", "David", "Elizabeth", "William", "Barbara", "Richard", "Susan", "Joseph", "Jessica", "Thomas", "Sarah", "Charles", "Karen"]
last_names = ["Smith", "Johnson", "Williams", "Brown", "Jones", "Garcia", "Miller", "Davis", "Rodriguez", "Martinez", "Hernandez", "Lopez", "Gonzalez", "Wilson", "Anderson", "Thomas", "Taylor", "Moore", "Jackson", "Martin"]
companies = ["Acme Corp", "Global Tech", "Initech", "Umbrella Corp", "Stark Industries", "Wayne Enterprises", "Cyberdyne Systems", "Massive Dynamic", "Hooli", "Pied Piper"]
statuses = ["New", "Contacted", "Qualified", "Lost"]

def generate_leads(num_leads):
    leads = []
    for _ in range(num_leads):
        first_name = random.choice(first_names)
        last_name = random.choice(last_names)
        email = f"{first_name.lower()}.{last_name.lower()}{random.randint(1,99)}@example.com"
        phone = f"555-{random.randint(100,999)}-{random.randint(1000,9999)}"
        company = random.choice(companies)
        status = random.choice(statuses)
        leads.append([first_name, last_name, email, phone, company, status])
    return leads

def save_csv(leads, filename):
    with open(filename, 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(["First Name", "Last Name", "Email", "Phone", "Company", "Status"])
        writer.writerows(leads)

def save_html(leads, filename):
    html = "<!DOCTYPE html>\n<html>\n<head>\n<style>\nbody { font-family: Arial, sans-serif; margin: 20px; }\ntable { border-collapse: collapse; width: 100%; }\ntd, th { border: 1px solid #ddd; text-align: left; padding: 8px; }\ntr:nth-child(even) { background-color: #f2f2f2; }\nth { background-color: #4CAF50; color: white; }\n</style>\n</head>\n<body>\n<h2>Demo Leads List</h2>\n<table>\n<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Status</th></tr>\n"
    for lead in leads:
        html += f"<tr><td>{lead[0]}</td><td>{lead[1]}</td><td>{lead[2]}</td><td>{lead[3]}</td><td>{lead[4]}</td><td>{lead[5]}</td></tr>\n"
    html += "</table>\n</body>\n</html>"
    
    with open(filename, 'w') as f:
        f.write(html)

for count in [50, 100, 500]:
    leads = generate_leads(count)
    save_csv(leads, f"demo_leads_{count}.csv")
    save_html(leads, f"demo_leads_{count}.html")
    print(f"Generated demo_leads_{count}.csv and demo_leads_{count}.html")
