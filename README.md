# Verbruik-PHP
Registreer tellerstanden van water, electriciteit, gas en zon. Hou je verbruik in de gaten.

# Uitleg
Verbruik-PHP is een kleine PHP site die makkelijk toelaat verbruik van electriciteit, gas en water alsook opbrengst van zonnepanelen te monitoren. De tool kan door meerdere gebruikers gebruikt worden waardoor vergelijken heel makkelijk wordt. Je kan immers moeilijk inschatten of jouw verbruik 'normaal' is, of het normaal is dat je dit jaar 10% hoger of lager zit dan vorig jaar, als je niks hebt om te vergelijken.

# Features
![Features](https://i.imgur.com/YDZuqui.png)

## Nieuwe invoer
Invoeren van een nieuwe tellerstand. 
Vul elk veld in, telkens wordt de laatste waarde getoond. Voor gas, electriciteit en zon is 1 decimale waarde voldoende. Voor water noteer je best 3 cijfers na de komma omdat je daar slechts ca 1m3 verbruikt per week.
Er werd officieus overeengekomen om elke zondag een nieuwe tellerstand in te voeren. Dagelijkse tellerstanden zorgen voor teveel pieken en dalen waardoor vergelijken moeilijk wordt. Indien iedereen elke zondag een tellerstand invoert levert dit de beste en mooiste vergelijkingsmogelijkheden.

## Grafiek per dag
Deze grafiek toont het verbruik van de laatste 30 dagen, met elke waarde per dag. Er wordt sowieso tem vandaag getoond, onafhankelijk of iedereen al een tellerstand invulde of niet. Vandaar de 0 waardes voor deze personen. 
Standaard wordt enkel jouw eigen verbruik getoond. Klik op een of meerdere namen om te vergelijken. Bij de naam wordt telkens de datum van de laatste tellerstand getoond.
Voor wat onze (Guy) tellerstanden betreft: deze worden automatisch in realtime ingevuld. Bij een manuele meteringave worden deze automatische ingevulde waardes vervangen door het gemiddelde, net als bij jullie dus. 

## Grafiek per maand
Toont het gemiddeld verbruik 'per lopende maand'. Hiermee bedoel ik dat er steeds tot de dag van de laatste tellerstand getoond wordt, telkens in periodes van 1 maand. Bijvoorbeeld op de 15de van de maand zie je het verbruik van de 15de van de vorige maand tem de 15 van deze maand etc. Standaard wordt enkel jouw eigen verbruik getoond. Klik op een of meerdere namen om te vergelijken. Bij de naam wordt telkens de datum van de laatste tellerstand getoond. Hier is het zo dat de periode mee schuift met de oudste tellerstand van elke geselecteerde gebruiker. Met de gele knop "12 maanden" kan je kiezen hoeveel maanden je wil zien.

## Grafiek per jaar
Toont het gemiddeld verbruik 'per lopend jaar'. Hiermee bedoel ik dat er steeds tot de dag van de laatste tellerstand getoond wordt, telkens in periodes van 1 jaar. Bijvoorbeeld op de 15 maart zie je het verbruik van de 15 maart vorige jaar tem de 15 maart van dit jaar etc. Hierdoor heb je op elk moment een zicht op een volledig jaar die alle seizoenen bevat. Standaard wordt enkel jouw eigen verbruik getoond. Klik op een of meerdere namen om te vergelijken. Bij de naam wordt telkens de datum van de laatste tellerstand getoond. Hier is het zo dat de periode mee schuift met de oudste tellerstand van elke geselecteerde gebruiker. Met de gele knop "10 jaar" kan je kiezen hoeveel jaar er getoond wordt. 

## Overzicht jaren
Toont een overzicht van de laatste jaren per gebruiker. In deze grafiek kan je dus niet vergelijken met andere gebruikers, maar vergelijk je met het verbruik van de voorbije jaren. Deze grafiek werkt niet met lopende maanden maar met de effectieve kalendermaanden. Hierdoor kan het dus zijn dat de huidige maand wijzigt naar mate de maand vordert. De volle lijn toont het effectieve verbruik voor elke maand, de stippellijn een lopend gemiddelde. In januari zullen beide punten dus steeds gelijk zijn. In februari toont de stippellijn het gemiddelde van januari en februari etc. Met de knop "3 jaar" kan je selecteren hoeveel jaren je wil tonen. Vooral voor deze grafiek geld dat het pas mooier wordt na enkele jaren gebruik en dat er regelmatig tellerstanden ingevuld worden.

## Temperaturen
Toont de minima, gemiddelde en maxima temperaturen per maand. Dit moet nog verder uitgewerkt worden. Deze gegevens kunnen handig zijn om te verduidelijken waarom een bepaalde 

## Grafiek in volume - Grafiek in euro
Selecteer welke waardes je wil zien in alle bovenstaande grafieken en tabellen. De waardes in euro worden berekend adhv de prijzen die je invoert.

## Prijzen invoeren
In de bovenste rij kan een nieuwe prijs ingevoerd worden. 
Er staat een soort standaard tarief ingesteld op datum van 1/1/2000. Vervolgens heb ik enkele van onze facturen bekeken en deze prijzen overgenomen voor iedereen, voel je vrij deze aan te passen, te verwijderen of andere toe te voegen. Indien een nieuwe prijs wordt toegevoegd zullen automatisch alle bedragen per dag berekend worden in de achtergrond. 
Voor het veld zon geld dat dit enkel dient ingevuld te worden indien er sprake is van groenestroom certificaten. De actuele electriciteitsprijs wordt automatisch meegenomen in de berekening. Dit getal zal dus steeds hetzelfde zijn en blijven totdat de certificaten verdwijnen.
*Let op:* alle prijzen dienen in eurocent ingevuld te worden, zonder komma of punt.  € 4,30 wordt dus gewoon 430.

## Wis tellerstand
Verwijder een tellerstand. De verbruiksgegevens worden automatisch opnieuw berekend indien je een van de laatste 5 tellerstanden wist. Wis je een oudere geef je beter een seintje zodat ik alles kan laten herberekenen.

## Verbruik der dag als CSV
Toont alle verbruiksgetallen in CSV zodat je deze zelf nog kan backuppen.

## Tellerstanden als CSV
Toont alle ingevoerde tellerstanden in CSV zodat je deze zelf nog kan backuppen.

# Installatie
Upload de bestanden naar een nieuwe map of een nieuw subdomein.
Pas de databaseconnectie aan in secure/settings.php en in de overige bestanden.
Voer secure/start.sql uit om de usertabel te maken. 
Vul een of meerdere gebruikers in de usertabel
Voer het secure/update.php script uit. Voor elke gebruiker worden de nodige tabellen gemaakt. 
