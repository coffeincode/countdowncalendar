# CountdownCalendar für Contao 4

Eine Advents- oder auch allgemeiner Countdown-Kalender-Erweiterung - das ist  meine kleine Übung, um tiefer in die Strukturen und Vorgehensweisen der Contao-Bundles einzutauchen. Das Bundle darf gerne verwendet werden und ich freue mich über Tipps und Anregungen, aber erwartet keinen fehlerlosen Proficode. Ich übernehme keine Garantie und rate davon ab, dieses Bundle in Produktivumgebungen zu nutzen. 



## Was ist der CountdownCalendar?

Diese Erweiterung ist eigentlich ein Adventskalender, aber um die Erweiterung ohne die Schlagworte "Advent" und "Weihnachten" auch im weiteren Jahreslauf nutzen zu können, wurde sie allgemeiner einfach CountdownCalendar genannt. 

## Konzept

Der CountdownCalendar wird im Frontend als Bild angezeigt, das mit "Türen" überlagert ist in Anlehnung an den klassischen Adventskalender. Die Tür des aktuellen Tages und alle davor öffnen sich, wenn der Mauszeiger darüber hovert. 
Es erscheint eine Schaltfläche, die ein Modal öffnet. Im Modal steht die Beschreibung des jeweiligen Türchens und ein Button zum Aufrufen des eigentlichen Türchen-Inhaltes. 

Im Backend sind die Kalender so organisiert:
Jeder Kalendertag hat ein Startdatum, dies ist aber nicht gleichzusetzen mit dem "published"-Häkchen, das den Kalendereintrag veröffentlicht. Um mehrere Kalendertage für ein und dasselbe Datum zu erlauben wurde das Published-Feld verwendet um anzuzeigen, ob diese Tür grundsätzlich im Frontend ausgegeben werden darf. Einen verfolgbaren Link und damit Zugriff auf die Inhalte des Türchens gibt es nur dann, wenn das Datum des Kalendertages erreicht oder überschritten wurde. Die Frontendvorschau funktioniert aus diesem Grund nur begrenzt, siehe Abschnitt "Testen und Debuggen"

### Innenleben

Der Kalender ist ähnlich wie ein Nachrichtenkanal inkl. Listing und Leser angelegt. 
Ein Kalender benötigt die Angaben, wann er startet und endet sowie das Bild, das im Hintergrund angezeigt werden soll. Zudem muss angegeben werden, welche Bildgröße verwendet werden soll und welche Breakpoints angewendet werden. 
Mit der Angabe, wie viele Türen pro Reihe angezeigt werden sollen (LG vs.  MD vs. XS), werden die Türbreiten dann entsprechend der Containergröße adaptiert. 
Die Farb-Attribute sind noch nicht funktional, derzeit muss entweder per zusätzlichem css-style im Backend oder per Anpassung der SCSS-Variablen im Modulcode die jeweilige Farbgebung angepasst werden. 

## Hintergrundbild

Der Kalender wurde so angelegt, dass ein Bild im Hintergrund das responsive Verhalten des Containers regelt. Es ist empfehlenswert, eine gesonderte Bildgröße anzulegen, mit der dieses Verhalten gesteuert wird. So kann sichergestellt
werden, dass der Kalendercontainer auch auf kleinen Displays genug Höhe zur Verfügung stellt, um die Türen ohne Überlagerungen neben- und untereinander darzustellen. 
Die Bildgröße sollte mit exakten Maßen arbeiten und mindestens 2 Breakpoints verarbeiten. Bei xs-Displays ist es empfehlenswert, das Kalenderbild ggf. vom Querformat auf Hochformat wechseln zu lassen, das ist mit responsive images in Contao ja kein Problem. 

## Anlegen neuer Kalender-Einträge

Die Kalender-Tage werden ähnlich wie Nachrichten eines Nachrichtenkanals angelegt, jeder Kalendertag hat ein Teaserfeld. Die Detail-Infos für die Leserseite sind als herkömmliche Content-Elements aus Contao auswählbar. 
Es empfiehlt sich, erst alle Tage des Kalenderzeitraums anzulegen, damit keiner vergessen wird. Danach kann die Ausgabereihenfolge für das Frontend durch Umsortieren der Liste durchgeführt werden. 


## Testen und Debuggen 

Der Kalender bringt einen Debug-Modus mit sich, um das Verhalten des Kalenders durchzutesten bevor die Kalenderdaten erreicht werden. 
Sobald der Kalender auf debug gesetzt wird, muss ein Datum angegeben werden, das ab sofort für alle Kalender-Anfragen im Frontend verwendet werden soll. So kann Anfang November getestet werden, ob die Türen am 22.12. alle korrekt öffnen und die letzten beiden wie erwartet verschlossen bleiben. 

Dies kann gemeinsam mit der Frontend-Vorschau in der Einstellung "uveröffentlichte Elemente anzeigen" verwendet werden.
Da der Debug-Modus die Frontend-Ausgabe für alle Aufrufe verändert, nicht nur für die Frontend-Vorschau, sollte er nachdem alles eingestellt wurde auch unbedingt wieder ausgeschaltet werden.




