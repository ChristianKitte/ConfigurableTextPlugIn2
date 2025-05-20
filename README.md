![cropped-BewerbungHeader1](https://github.com/user-attachments/assets/de3a724b-4002-4e2b-80a4-e9d588bf1930)

# Flexibles Text-Plugin – Anpassbare Textausgabe mit Stil

## Einführung

**Flexibles Text-Plugin** ist ein WordPress-Plugin, das es Ihnen ermöglicht, konfigurierbare Textblöcke über anpassbare Shortcodes auf Ihrer Webseite auszugeben. Das Plugin bietet detaillierte Anpassungsmöglichkeiten für Schriftarten, Textausrichtung und sogar 3D-Rotationseffekte (X-, Y-, Z-Achse). Die Verwaltung erfolgt über eine moderne, auf Bootstrap basierende Benutzeroberfläche im WordPress-Adminbereich. Es ist ideal für Benutzer, die dynamische und stilistisch angepasste Textelemente einfach und ohne Programmieraufwand erstellen möchten.

## Funktionen

* **Mehrere Textinstanzen:** Erstellen Sie beliebig viele unabhängige Textkonfigurationen.
* **Anpassbare Shortcodes:** Jede Textinstanz generiert einen eigenen Shortcode, dessen Name Sie anpassen können.
* **Detaillierte Schriftanpassung:**
   * Schriftfamilie
   * Schriftgröße (mit Einheit, z.B. `px`, `em`, `%`)
   * Schriftgewicht
   * Schriftstil (normal, kursiv, etc.)
   * Textfarbe
   * Zeilenhöhe
* **Textausrichtung:** Links, zentriert oder rechts.
* **3D-Rotationseffekte:**
   * Rotationsgeschwindigkeit (Umdrehungen pro Minute)
   * Rotationsachse (X, Y, Z)
   * Rotationsrichtung (im oder gegen den Uhrzeigersinn)
* **Inhaltsoptionen:**
   * Formatierung beibehalten (Zeilenumbrüche, Leerzeichen)
   * HTML-Inhalt rendern (mit Vorsicht verwenden)
* **Moderne Admin-Oberfläche:**
   * Bootstrap 5 basiertes Design.
   * Tab-Navigation zur einfachen Verwaltung mehrerer Instanzen.
   * AJAX-gestütztes Speichern und Löschen für eine flüssige Bedienung.
   * Visuelles Feedback bei ungespeicherten Änderungen.
* **Internationalisierung:** Bereit für Übersetzungen (`.pot`-Datei enthalten).

## Installation

1.  Laden Sie den Plugin-Ordner `configurable-text-plugin` in das Verzeichnis `/wp-content/plugins/` hoch.
    *Oder:*
2.  Navigieren Sie im WordPress-Adminbereich zu `Plugins` > `Installieren`.
3.  Klicken Sie auf `Plugin hochladen`, wählen Sie die ZIP-Datei des Plugins aus und klicken Sie auf `Jetzt installieren`.
4.  Aktivieren Sie das Plugin über das Menü `Plugins` in WordPress.

## Bedienung

### 1. Zugriff auf die Einstellungsseite

Nach der Aktivierung finden Sie die Einstellungsseite des Plugins unter:
`Einstellungen` > `Configurable Text` im WordPress-Adminbereich.

### 2. Instanzen verwalten

Auf der Einstellungsseite können Sie verschiedene Textinstanzen erstellen und konfigurieren. Jede Instanz repräsentiert einen einzigartigen Textblock mit eigenen Einstellungen und einem eigenen Shortcode.

* **Neue Instanz hinzufügen:**
   * Klicken Sie auf den Button `Add New Text Instance`.
   * Es wird ein neuer Tab für die neue Instanz erstellt.
   * Konfigurieren Sie die Instanz wie unten beschrieben.
   * Klicken Sie auf `Save Instance` innerhalb des Tabs der Instanz, um die Einstellungen zu speichern.
* **Instanz konfigurieren:**
   * Wählen Sie den Tab der gewünschten Instanz aus.
   * Passen Sie die Einstellungen im Formular an (siehe Abschnitt "Konfiguration einer Textinstanz" für Details).
   * Der `Save Instance`-Button wird gelb hervorgehoben, sobald Änderungen vorgenommen wurden.
* **Instanz speichern:**
   * Klicken Sie auf den `Save Instance`-Button innerhalb des jeweiligen Instanz-Tabs. Eine Erfolgsmeldung wird angezeigt.
* **Instanz löschen:**
   * Klicken Sie auf den `Delete Instance`-Button innerhalb des jeweiligen Instanz-Tabs.
   * Bestätigen Sie die Löschananfrage.

### 3. Shortcodes verwenden

Nachdem Sie eine Instanz gespeichert haben, wird Ihnen der zugehörige Shortcode im Einstellungsformular angezeigt (z.B. `[configurable_text_1]`).

* Kopieren Sie diesen Shortcode.
* Fügen Sie ihn in den Inhalt einer Seite, eines Beitrags oder eines Widgets ein, wo der konfigurierte Text angezeigt werden soll.

## Konfiguration einer Textinstanz

Jede Textinstanz kann individuell über die folgenden Felder im WordPress-Adminbereich (`Einstellungen > Configurable Text > [Ihr Instanz-Tab]`) angepasst werden:

| Feld                        | Beschreibung                                                                                                |
| :-------------------------- | :---------------------------------------------------------------------------------------------------------- |
| **Allgemein** |                                                                                                             |
| `Instance Name`             | Ein Name zur Identifizierung dieser Instanz im Backend (z.B. "Willkommensgruß Startseite").                 |
| `Text to Display`           | Der eigentliche Text, der durch den Shortcode angezeigt werden soll.                                        |
| `Preserve Formatting`       | (Checkbox) Wenn aktiviert, bleiben Zeilenumbrüche und mehrfache Leerzeichen aus dem Textfeld erhalten.      |
| `Render HTML Content`       | (Checkbox) Wenn aktiviert, wird der eingegebene Text als HTML interpretiert. Mit Vorsicht verwenden!        |
| **Shortcode & Ausrichtung** |                                                                                                             |
| `Shortcode Name`            | Der Name des Shortcodes (z.B. `mein_spezial_text`). Standardmäßig `configurable_text_X`. Nur Buchstaben, Zahlen und Unterstriche. |
| `Text Alignment`            | Ausrichtung des Textes: `Left`, `Center`, `Right`.                                                          |
| **3D-Rotationseffekte** |                                                                                                             |
| `Rotation Speed (RPM)`      | Geschwindigkeit der 3D-Rotation in Umdrehungen pro Minute. `0` deaktiviert die Rotation.                    |
| `Rotation Axis`             | Die Achse, um die der Text rotiert: `X-Axis`, `Y-Axis`, `Z-Axis`.                                           |
| `Rotation Direction`        | Richtung der Rotation: `Clockwise` (im Uhrzeigersinn), `Counterclockwise` (gegen den Uhrzeigersinn).          |
| **Schriftarteinstellungen** |                                                                                                             |
| `Font Family`               | Wählen Sie eine Schriftfamilie (z.B. Arial, Georgia, Times New Roman).                                       |
| `Font Size`                 | Schriftgröße mit Einheit (z.B. `16px`, `1.2em`, `100%`).                                                     |
| `Font Weight`               | Schriftgewicht (z.B. Normal, Bold, 100-900).                                                                |
| `Font Style`                | Schriftstil (z.B. Normal, Italic, Oblique).                                                                 |
| `Text Color`                | Farbe des Textes (Auswahl über Farbpicker oder Hex-Code).                                                   |
| `Line Height`               | Zeilenhöhe des Textes (z.B. `1.5`, `2`, `150%`).                                                             |

> **Hinweis:** Nachdem Sie eine neue Instanz hinzugefügt oder Änderungen an einer bestehenden Instanz vorgenommen haben, klicken Sie immer auf den `Save Instance`-Button innerhalb des jeweiligen Tabs, um Ihre Konfiguration zu speichern.

## Technische Hinweise

* **Abhängigkeiten:** Das Plugin verwendet Bootstrap 5.3.0 (via CDN) für die Gestaltung der Admin-Oberfläche und jQuery (standardmäßig in WordPress enthalten) für AJAX-Operationen.
* **Datenhaltung:** Alle Konfigurationen der Textinstanzen werden in der WordPress-Optionentabelle (`wp_options`) gespeichert.
* **Sicherheit:** Benutzereingaben werden serverseitig validiert und bereinigt (z.B. mittels `sanitize_text_field`, `wp_kses_post`). Nonces werden zur Absicherung von Formularübermittlungen verwendet.
* **Performance:** Frontend-CSS für Rotationen wird nur geladen, wenn mindestens eine Instanz eine Rotationsgeschwindigkeit > 0 hat. Die Styles werden dynamisch generiert und sind spezifisch für die konfigurierten Instanzen.

## Lizenz

Hiermit wird das Recht gewährt, diese Software für ausschließlich private und nicht-kommerzielle Zwecke kostenlos zu nutzen, zu kopieren und zu installieren.

Jede Form der kommerziellen Nutzung, sei es direkt oder indirekt, sowie die Nutzung in einem geschäftlichen, beruflichen oder gewerblichen Kontext, ist ohne eine 
vorherige schriftliche Lizenzvereinbarung ausdrücklich untersagt.

DIE SOFTWARE WIRD OHNE MÄNGELGEWÄHR UND OHNE JEGLICHE AUSDRÜCKLICHE ODER STILLSCHWEIGENDE GEWÄHRLEISTUNG, EINSCHLIESSLICH, ABER NICHT BESCHRÄNKT AUF DIE GEWÄHRLEISTUNG 
DER MARKTGÄNGIGKEIT, DER EIGNUNG FÜR EINEN BESTIMMTEN ZWECK UND DER NICHTVERLETZUNG VON RECHTEN DRITTER, ZUR VERFÜGUNG GESTELLT. IN KEINEM FALL SIND DIE AUTOREN ODER 
URHEBERRECHTSINHABER FÜR ANSPRÜCHE, SCHÄDEN ODER ANDERE VERPFLICHTUNGEN HAFTBAR, SEI ES AUS VERTRAG, UNERLAUBTER HANDLUNG ODER ANDERWEITIG, DIE SICH AUS ODER IN VERBINDUNG 
MIT DER SOFTWARE ODER DER NUTZUNG ODER ANDEREN GESCHÄFTEN MIT DER SOFTWARE ERGEBEN.
