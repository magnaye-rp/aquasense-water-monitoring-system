# Aquasense — Water Monitoring & Automation System

**Aquasense** is an IoT-powered water quality monitoring and automation system designed to measure **pH level, turbidity, and temperature** in real time using ESP32 or NodeMCU microcontrollers. It includes an interactive web dashboard built with **CodeIgniter 4** and **Shield authentication**, allowing users to monitor sensor data, view historical logs, and control connected devices such as **pumps and oxygenators** through relay modules.

The system also supports **smart automation**, triggering actions and generating alerts based on configurable thresholds (e.g., low pH, high turbidity, or temperature deviations).

---

## Features

- 🌡️ **Real-time monitoring** of pH, turbidity, and temperature  
- 📈 **Sensor data logging** with historical records  
- ⚙️ **Device control** for pumps & oxygenators via relay  
- 🤖 **Automation logic** with alerts when thresholds are exceeded  
- 🔐 **Secure login** using CodeIgniter Shield  
- 🖥️ **Web dashboard** with gauges and status indicators  
- 📢 **Notifications & alerts** for automated events  
- 🔌 **JSON API** for ESP32/NodeMCU data submission  

---

## Tech Stack

- **Backend:** CodeIgniter 4  
- **Frontend:** HTML, Bootstrap/Tailwind  
- **Authentication:** CodeIgniter Shield  
- **Database:** MySQL  
- **Microcontroller:** ESP32 / NodeMCU  
- **Sensors:** pH, turbidity, temperature  
- **Relay Modules:** For pump & oxygenator control  

---

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/aquasense-water-monitoring-system.git
cd aquasense-water-monitoring-system

