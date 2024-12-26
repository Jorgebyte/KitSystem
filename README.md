# KitSystem

This plugin makes it easy for you to manage kits as it has a form system for easier kit creation (This plugin is inspired by the EasyKits Plugin)

# Features
- Easy to understand forms
- SQLite3 Database
- Separation of kits by categories
- Improved kit and category editing system
- JSON Kit Storage
- Starter Kit
- Supports multiple economies
- Custom messages
- PHPStan level max compliance for improved code quality and robustness
- More features coming soon

# Commands
- `/kit` Opens a form with the available kits
- `/kit create` Opens a form to create the kits
- `/kit delete` Opens a form to delete kits
- `/kit give` Open a form with the name of the kits and players, so we can send you the kits
-  `/kit giveall` Open a form with kit names to give to all players
- `/kit createcategory` Opens a form to create a category for the kits (this helps to sort the kits)
- `/kit deletecategory` Opens a form with all categories to delete a category
- `/kit edit` Opens a subform for you to choose what to edit from the kit
- `/kit editcategory` Opens a subform for you to choose what to edit from the category
- `/kit preview` Opens a form to select a kit and view its items

# Libs: 
## Command: [Commando](https://github.com/LatamPMDevs/Commando)
## Forms: [EasyUI](https://github.com/Jorgebyte/easyui)
## Items: [item-serialize-utils](https://github.com/presentkim-pm/item-serialize-utils.git)
## Economy: [libPiggyEconomy](https://github.com/DaPigGuy/libPiggyEconomy.git)
## InvMenu: [InvMenu](https://github.com/Muqsit/InvMenu.git)

# Review:

[![Alt text](https://img.youtube.com/vi/f-7IVLkiZFQ/0.jpg)](https://www.youtube.com/watch?v=f-7IVLkiZFQ)

# Permissions
```YAML
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

# Contact
[![Discord Presence](https://lanyard.cnrad.dev/api/1165097093480853634?theme=dark&bg=005cff&animated=false&hideDiscrim=true&borderRadius=30px&idleMessage=Hello)](https://discord.com/users/1165097093480853634)
