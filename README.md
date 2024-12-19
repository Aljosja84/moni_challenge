## Moni Challenge

Dit is mijn entry voor de Moni Calendar Code Challenge. 

## Assignments
1. Create a Symfony application with a Command class that can be executed from the
command line interface. The Command must accept an optional parameter with the
e-mail address of the consultant to export all their planned meetings to a file. If the
parameter is omitted, all meetings must be exported to one file per consultant.
Bonus: Use an output formatter to show what the Command is doing at a given time.
2. Create a Symfony Controller that can be called with the e-mail address of a
consultant as a parameter. The Controller will display only the meetings of the current
week. No authentication or authorization is required.
3. Add a button to the Controller to edit a form that allows you to edit and save one
meeting entry.
4. Bonus: Consolidate the code by creating a service that can be called from both the
Command and the Controller and that is responsible for retrieving the meetings from
the database and giving them to the Command/Controller for further processing.


## Mijn ervaringen

Ik heb alle assignments afgerond behalve nr.4 toen ik nog maar 7 minuten over had van de 4 uur die ik ervoor heb uitgetrokken. Ik heb de meeste tijd besteed aan de [Command class](https://github.com/Aljosja84/moni_challenge/blob/main/app/Console/Commands/ExportMeetings.php). Ik heb in het verleden wel eens een Command class gemaakt
voor een twitter wrapper en een notificatie systeem maar dat is een beetje verstoft. Dus ik vond het wel spannend om het weer op te pakken.

Ik heb geen aparte package voor het maken van iCal files geinstalleerd. De RFC 5545 format zijn maar een paar regels
die ik heb gehardcode in de command class zelf voor het gemak. Laravel maakt gebruik van Symfony's Console, dus het was vrij gemakkelijk om de output formatter te gebruiken voor feedback aan de users dmv info en progress bars.

Hieronder een video van de Command class in actie zonder een email parameter

https://github.com/user-attachments/assets/1518fb16-e837-4f20-bf40-d082becacddf

Met hieronder een video van de Command class in actie met een email parameter

https://github.com/user-attachments/assets/bf2a99d0-5cc0-4414-9b41-0943732c6859

De [Controller](https://github.com/Aljosja84/moni_challenge/blob/main/app/Http/Controllers/MeetingController.php) was pretty straightforward, alleen redelijk veel tijd nog besteed aan opmaak van de blade files:

![searchBladeFile](https://github.com/user-attachments/assets/28fdacb5-39fc-46b5-9a5a-c1c0e7b05992)

User 8 was de enige die deze week meetings had

![meetingsUser8](https://github.com/user-attachments/assets/ff109dc7-fdfc-48f0-8bec-a8ec8a35312c)

Editing a meeting

![editMeeting](https://github.com/user-attachments/assets/7babced2-84c9-4d7f-b74b-3da5bc642dc7)

Achteraf zou ik willen dat ik meer tijd had besteed aan assignment #4: het maken van een Service. Ik heb achteraf de documentatie ervoor nog nagelezen 
en het heeft zeer veel voordelen:
1. Seperation of Concern
2. Code reusability
3. Improved testability
4. Scalability

Het gebruik ervan had de Command en Controller heel wat schoner opgeleverd. 

Al met al ben ik niet geheel ontevreden over het resultaat van de challenge!





