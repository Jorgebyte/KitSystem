
---

# 🌟 KitSystem
[![](https://poggit.pmmp.io/shield.state/KitSystem)](https://poggit.pmmp.io/p/KitSystem)

**KitSystem** is a powerful and user-friendly PocketMine-MP plugin that allows you to manage kits with ease using intuitive form-based interfaces.
Inspired by the [EasyKits](https://github.com/AndreasHGK/EasyKits) plugin, but rebuilt from scratch with performance and scalability in mind.

---

## ✨ Features

* 🔹 Easy-to-use and intuitive UI for creating and managing kits
* 💾 Support for **SQLite** and **MySQL** via `libasynql`
* 📂 Kits organized by **categories**
* 🛠️ Advanced editing system for kits and categories
* 🌐 Multi-language support
* 🎁 Starter kit support
* 💰 Compatible with **multiple economies** via `libPiggyEconomy`
* 📝 Fully customizable messages
* 🧼 Code quality verified at **PHPStan max level**
* 🧩 Modular and scalable design
* 🔜 More features coming soon!

---

## 📜 Commands

| Command               | Description                                             |
| --------------------- | ------------------------------------------------------- |
| `/kit`                | Opens the main form with available kits                 |
| `/kit create`         | Opens the form to create a new kit                      |
| `/kit delete`         | Opens the form to delete an existing kit                |
| `/kit give`           | Opens a form to select a kit and player to give it to   |
| `/kit giveall`        | Opens a form to select a kit and give it to all players |
| `/kit createcategory` | Opens a form to create a new kit category               |
| `/kit deletecategory` | Opens a form to delete a kit category                   |
| `/kit edit`           | Opens a form to select and edit a kit                   |
| `/kit editcategory`   | Opens a form to select and edit a category              |
| `/kit preview`        | Opens a form to preview the items of a kit              |

---

## 🧱 Dependencies

This plugin uses several open-source libraries to ensure flexibility and scalability:

* 📦 Command Framework: [`Commando`](https://github.com/LatamPMDevs/Commando)
* 🖼️ Form API: [`EasyUI`](https://github.com/Jorgebyte/easyui)
* 🧪 Item Serialization: [`item-serialize-utils`](https://github.com/presentkim-pm/item-serialize-utils)
* 💰 Economy Support: [`libPiggyEconomy`](https://github.com/DaPigGuy/libPiggyEconomy)
* 🗃️ Inventory Menus: [`InvMenu`](https://github.com/Muqsit/InvMenu)
* 🧵 Async Database Queries: [`libasynql`](https://github.com/poggit/libasynql)
* 🌍 Multi-language Support: [`Languages`](https://github.com/IvanCraft623/languages)

---

## 🛡️ Permissions

```yaml
kitsystem.command:
  default: true
kitsystem.command.create:
  default: op
kitsystem.command.delete:
  default: op
kitsystem.command.give:
  default: op
kitsystem.command.giveall:
  default: op
kitsystem.command.createcategory:
  default: op
kitsystem.command.deletecategory:
  default: op
kitsystem.command.editkit:
  default: op
kitsystem.command.editcategory:
  default: op
kitsystem.command.previewkit:
  default: true
```

---

## 🎥 Preview

[![KitSystem Preview](https://img.youtube.com/vi/f-7IVLkiZFQ/0.jpg)](https://www.youtube.com/watch?v=f-7IVLkiZFQ)

---

## 📞 Contact

Need help or have suggestions?

[![Discord Presence](https://lanyard.cnrad.dev/api/1165097093480853634?theme=dark\&bg=005cff\&animated=false\&hideDiscrim=true\&borderRadius=30px\&idleMessage=Hello)](https://discord.com/users/1165097093480853634)
