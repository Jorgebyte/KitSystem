# 📋 Documentación del Sistema KitSystem

## 🔍 ¿Qué hace este código?

**KitSystem** es un plugin para servidores de Minecraft PocketMine-MP que permite gestionar "kits" (conjuntos predefinidos de objetos y armaduras) de manera eficiente y amigable para el usuario.

### Funcionalidades Principales:

1. **Gestión de Kits**: Creación, edición, eliminación y distribución de kits de objetos
2. **Sistema de Categorías**: Organización de kits en categorías para mejor navegación
3. **Interfaz de Usuario**: Formularios intuitivos para la interacción con jugadores y administradores
4. **Sistema de Economía**: Integración con múltiples sistemas económicos para kits de pago
5. **Sistema de Cooldowns**: Control de tiempo entre usos de kits por jugador
6. **Soporte Multiidioma**: Traducciones para múltiples idiomas
7. **Persistencia de Datos**: Almacenamiento en bases de datos SQLite o MySQL
8. **Sistema de Permisos**: Control granular de acceso a funcionalidades

---

## ⚙️ ¿Cómo funciona?

### Arquitectura del Sistema

El plugin está estructurado en una arquitectura modular con separación clara de responsabilidades:

#### 1. **Clase Principal (`Main.php`)**
```php
final class Main extends PluginBase {
    // Punto de entrada del plugin
    // Inicialización de componentes
    // Gestión del ciclo de vida del plugin
}
```

**Responsabilidades:**
- Bootstrapping del plugin
- Inicialización de hooks (PacketHooker, InvMenu)
- Configuración de la base de datos
- Carga de traducciones
- Registro de comandos y eventos

#### 2. **Gestores (Managers)**

##### **KitManager**
- Gestiona todos los kits del sistema
- Carga y persistencia de datos
- Distribución de kits a jugadores
- Serialización/deserialización de objetos

##### **CategoryManager**
- Administra las categorías de kits
- Relaciones entre categorías y kits
- Persistencia de categorías

##### **CooldownManager**
- Control de tiempos de espera
- Verificación de cooldowns por jugador
- Limpieza automática de cooldowns expirados

#### 3. **Sistema de Formularios (`form/`)**
```
FormManager -> FormTypes (enum) -> Formularios específicos
```

**Tipos de formularios:**
- **KitsForm**: Lista principal de kits disponibles
- **CreateKitForm**: Creación de nuevos kits
- **EditKitDataForm**: Edición de datos de kits
- **CategoryForm**: Gestión de categorías
- **SelectKitForm**: Selección de kits específicos

#### 4. **Sistema de Comandos (`command/`)**
```
KitSystemCommand (comando raíz)
├── CreateKitCommand
├── DeleteKitCommand
├── EditKitCommand
├── GiveKitCommand
├── GiveAllKitCommand
├── PreviewKitCommand
├── CreateCategoryCommand
├── DeleteCategoryCommand
└── EditCategoryCommand
```

#### 5. **Capa de Datos**
- **SQLite**: Para instalaciones simples
- **MySQL**: Para servidores de alta carga
- **Tablas principales**:
  - `kits`: Almacena información de kits
  - `categories`: Información de categorías
  - `category_kits`: Relación many-to-many
  - `cooldowns`: Tiempos de espera por jugador

### Flujo de Ejecución

1. **Inicialización**:
   ```
   onLoad() -> onEnable() -> Inicialización de componentes
   ```

2. **Comando del jugador**:
   ```
   /kit -> KitSystemCommand -> FormManager -> KitsForm -> Jugador
   ```

3. **Uso de kit**:
   ```
   Selección -> Verificación permisos -> Check cooldown -> Check economía -> Distribución
   ```

### Dependencias Externas

| Dependencia | Propósito |
|------------|-----------|
| `Commando` | Framework de comandos |
| `EasyUI` | Sistema de formularios |
| `item-serialize-utils` | Serialización de objetos |
| `libPiggyEconomy` | Integración económica |
| `InvMenu` | Menús de inventario |
| `libasynql` | Consultas asíncronas a BD |
| `Languages` | Soporte multiidioma |

---

## 🔧 Puntos a Mejorar

### 1. **Arquitectura y Diseño**

#### **Problemas Identificados:**
- **Singleton Pattern en Main**: Uso del trait `SingletonTrait` puede dificultar testing
- **Dependencias acopladas**: Algunos managers dependen directamente de `Main::getInstance()`
- **Falta de interfaces**: No hay contratos claros para los managers

#### **Soluciones Sugeridas:**
```php
// Implementar inyección de dependencias
interface KitManagerInterface {
    public function createKit(Kit $kit): Promise;
    public function getKit(string $name): ?Kit;
}

class KitManager implements KitManagerInterface {
    public function __construct(
        private DataConnector $database,
        private Translator $translator
    ) {}
}
```

### 2. **Gestión de Errores**

#### **Problemas:**
- Manejo inconsistente de excepciones
- Falta de logging detallado
- Errores silenciados en algunos métodos

#### **Mejoras:**
```php
// Implementar un sistema de logging robusto
class KitManager {
    public function createKit(Kit $kit): Promise {
        try {
            // Lógica de creación
        } catch (DatabaseException $e) {
            $this->logger->error("Failed to create kit: " . $e->getMessage());
            throw new KitCreationException("Could not create kit", 0, $e);
        }
    }
}
```

### 3. **Rendimiento**

#### **Optimizaciones Sugeridas:**

##### **Cache en Memoria:**
```php
class KitManager {
    private array $kitCache = [];
    
    public function getKit(string $name): ?Kit {
        if (isset($this->kitCache[$name])) {
            return $this->kitCache[$name];
        }
        // Cargar de BD y cachear
    }
}
```

##### **Carga Lazy:**
- Cargar kits solo cuando se necesiten
- Paginación en formularios con muchos kits
- Cache de consultas frecuentes

### 4. **Seguridad**

#### **Vulnerabilidades Potenciales:**
- Validación insuficiente de entrada de usuario
- Posible inyección SQL (aunque se usa prepared statements)
- Falta de rate limiting en comandos

#### **Soluciones:**
```php
class InputValidator {
    public static function validateKitName(string $name): bool {
        return preg_match('/^[a-zA-Z0-9_-]{1,32}$/', $name) === 1;
    }
    
    public static function sanitizeInput(string $input): string {
        return filter_var($input, FILTER_SANITIZE_STRING);
    }
}
```

### 5. **Testing**

#### **Problemas:**
- **No hay tests unitarios**
- **No hay tests de integración**
- **Difícil testear por dependencias acopladas**

#### **Solución:**
```php
// Estructura de tests sugerida
tests/
├── unit/
│   ├── KitManagerTest.php
│   ├── CategoryManagerTest.php
│   └── CooldownManagerTest.php
├── integration/
│   ├── DatabaseTest.php
│   └── FormIntegrationTest.php
└── fixtures/
    └── sample_data.php
```

### 6. **Configuración**

#### **Mejoras:**
- Validación de configuración al inicio
- Configuración en tiempo real sin reinicio
- Configuración por categoría/kit individual

```php
class ConfigValidator {
    public function validate(array $config): array {
        $errors = [];
        
        if (!isset($config['database']['type'])) {
            $errors[] = "Database type is required";
        }
        
        return $errors;
    }
}
```

### 7. **Documentación del Código**

#### **Necesidades:**
- **PHPDoc más detallado** en métodos complejos
- **Ejemplos de uso** en comentarios
- **Documentación de API** para desarrolladores

```php
/**
 * Creates a new kit with the specified configuration.
 * 
 * @param string $name Kit identifier (must be unique)
 * @param string $prefix Display name for the kit
 * @param Item[] $items Array of items to include
 * @param Item[] $armor Array of armor pieces [helmet, chestplate, leggings, boots]
 * @param int|null $cooldown Cooldown in seconds (null = no cooldown)
 * @param float|null $price Cost in economy units (null = free)
 * 
 * @return Promise<Kit> Resolves with the created kit
 * @throws KitCreationException When kit creation fails
 * 
 * @example
 * $kitManager->createKit(
 *     "warrior",
 *     "§cWarrior Kit",
 *     [VanillaItems::IRON_SWORD(), VanillaItems::BREAD()->setCount(16)],
 *     [VanillaItems::IRON_HELMET(), VanillaItems::IRON_CHESTPLATE()],
 *     3600, // 1 hour cooldown
 *     100.0 // 100 coins
 * );
 */
public function createKit(/* parameters */): Promise {
    // Implementation
}
```

### 8. **Métricas y Monitoreo**

#### **Implementar:**
- Métricas de uso de kits
- Monitoreo de rendimiento de BD
- Logs de actividad de administradores

```php
class MetricsCollector {
    public function trackKitUsage(string $kitName, Player $player): void {
        // Registrar uso para análisis
    }
    
    public function getPopularKits(int $limit = 10): array {
        // Retornar kits más usados
    }
}
```

### 9. **Escalabilidad**

#### **Consideraciones:**
- **Sharding de base de datos** para servidores múltiples
- **Cache distribuido** (Redis)
- **Microservicios** para funcionalidades específicas

### 10. **API para Desarrolladores**

#### **Crear:**
```php
interface KitSystemAPI {
    public function getKits(): array;
    public function giveKit(Player $player, string $kitName): bool;
    public function hasKit(Player $player, string $kitName): bool;
    public function registerKitUsageListener(callable $callback): void;
}
```

---

## 📊 Resumen de Mejoras Prioritarias

| Prioridad | Mejora | Impacto | Dificultad |
|-----------|--------|---------|------------|
| **Alta** | Implementar tests unitarios | Alto | Media |
| **Alta** | Mejorar manejo de errores | Alto | Baja |
| **Alta** | Validación de entrada | Alto | Baja |
| **Media** | Refactoring de arquitectura | Alto | Alta |
| **Media** | Sistema de cache | Medio | Media |
| **Media** | Documentación API | Medio | Baja |
| **Baja** | Métricas y monitoreo | Medio | Media |
| **Baja** | Microservicios | Alto | Muy Alta |

---

## 🎯 Conclusión

El **KitSystem** es un plugin bien estructurado con una arquitectura sólida y funcionalidades completas. Sin embargo, hay oportunidades significativas de mejora en áreas como testing, manejo de errores, rendimiento y documentación. Las mejoras sugeridas ayudarían a hacer el código más mantenible, seguro y escalable.

La implementación actual demuestra buenas prácticas en muchas áreas, especialmente en la separación de responsabilidades y el uso de patrones establecidos. Con las mejoras propuestas, podría convertirse en un plugin de referencia en el ecosistema PocketMine-MP.