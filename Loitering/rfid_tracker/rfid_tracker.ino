#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 5
#define RST_PIN 22
MFRC522 rfid(SS_PIN, RST_PIN);

const char* ssid = "Waeyo";
const char* password = "Miras09020821''";

const int buzzerPin = 14;
const int tableID = 1;  // palitan kada table

void setup() {
  Serial.begin(115200);
  pinMode(buzzerPin, OUTPUT);
  digitalWrite(buzzerPin, LOW);

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected.");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  SPI.begin();
  rfid.PCD_Init();
  Serial.println("RFID reader initialized.");
}

void loop() {
  // Check timer every 5 seconds
  static unsigned long lastCheck = 0;
  if (millis() - lastCheck > 2000) {
    lastCheck = millis();
    checkTimer();
  }

  // RFID detection
  if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
      uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    Serial.println("UID detected: " + uid);
    sendUID(uid);
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
  }
}

void checkTimer() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = "http://192.168.100.151/Loitering/esp_api/check_timer.php?table=" + String(tableID);
  http.begin(url);
  int code = http.GET();
  int rem = 0;
  
  if (code == 200) {
    String s = http.getString();
    s.trim();
    rem = s.toInt();
    Serial.println("Time left: " + String(rem) + "s");
    if (rem > 0 && rem <= 15) {
      triggerBuzzer();
    }
  } else {
    Serial.println("Timer HTTP error: " + String(code));
  }
  http.end();

  // ✅ Only update status if time is not zero
  if (rem > 0) {
    HTTPClient ping;
    String pingURL = "http://192.168.100.151/Loitering/esp_api/update_status.php?table=" + String(tableID);
    ping.begin(pingURL);
    ping.GET();
    ping.end();
  }
}

// send UID, then buzz if the response contains "Time added"
void sendUID(const String& uid) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = "http://192.168.100.151/Loitering/esp_api/rfid_event.php?uid=" + uid + "&table=" + String(tableID);
  http.begin(url);
  int code = http.GET();

  if (code == 200) {
    String resp = http.getString();
    resp.trim();
    Serial.println("RFID Response: " + resp);

    if (resp.indexOf("Time added") >= 0) {
      successBuzz();
    } else if (resp.indexOf("No time left") >= 0 || resp.indexOf("UID not found") >= 0) {
      errorBuzz(); // error feedback when invalid
    }

  } else {
    Serial.println("RFID HTTP error: " + String(code));
    errorBuzz(); // buzz if server fails
  }

  http.end();
}


void triggerBuzzer(){
  tone(buzzerPin, 100,800);
  delay(150);
  noTone(buzzerPin);
}
void successBuzz(){
  tone(buzzerPin, 500,200);
  delay(150);
  noTone(buzzerPin);
  tone(buzzerPin, 500,200);
  delay(150);
  noTone(buzzerPin);
}
void errorBuzz() {
  tone(buzzerPin, 200, 600); // long low beep
  delay(700);
  noTone(buzzerPin);
}
