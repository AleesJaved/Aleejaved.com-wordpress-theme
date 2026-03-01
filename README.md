# Aleejaved.com WordPress Theme

A modern, customizable WordPress portfolio theme with clean design and extensive customization options.

## Features

- **Customizable Colors** - Full control over accent, background, text, muted text, and border colors
- **Hero Section** - Customizable greeting, name, and subtitle with smart text wrapping
- **Portfolio Buttons** - Dynamic button management with custom names and links
- **Timeline System** - Sortable timeline with impressiveness ratings
- **Birthday Cards** - Interactive card creation system with scrapbook animations
- **Popup System** - Custom popup management with white theme styling
- **Page Order Management** - Drag-and-drop page ordering in WordPress admin
- **Responsive Design** - Mobile-friendly layout with proper breakpoints
- **SEO Optimized** - Semantic HTML5 structure and proper meta tags

## Customization Options

### Colors
- **Accent Color** - Links, buttons, highlights, and interactive elements
- **Background Color** - Main site background
- **Text Color** - Headings, paragraphs, and main content
- **Muted Text Color** - Subtitles, metadata, and secondary information
- **Border Color** - Card borders, dividers, and UI elements

### Hero Section
- **Greeting Text** - Customizable opening message (default: "Hello!")
- **Name Text** - Customizable name with smart line breaking
- **Subtitle Text** - Customizable description text

### Portfolio Links
- **Dynamic Buttons** - Add/remove buttons with custom names and links
- **Email Field** - Separate email configuration
- **Location Field** - Customizable location display

### Header
- **Site Name** - Customizable header name
- **Navigation** - WordPress pages with customizable order

## Installation

1. Upload the `aleejaved-portfolio` folder to `/wp-content/themes/`
2. Activate the theme in WordPress admin under **Appearance → Themes**
3. Customize colors and content under **Appearance → Customize**

## Theme Structure

```
aleejaved-portfolio/
├── assets/                 # JavaScript and CSS files
│   ├── nav-highlight.js   # Navigation active section highlighting
│   ├── popup.js           # Popup functionality
│   ├── timeline.js        # Timeline interactions
│   └── timeline-admin.js  # Timeline admin interface
├── functions.php          # Theme functions and customizer settings
├── style.css              # Main stylesheet with CSS variables
├── front-page.php         # Homepage template with hero section
├── header.php             # Site header with navigation
├── footer.php             # Site footer
├── index.php              # Blog index template
├── page.php               # Page template
├── single.php             # Single post template
├── single-aj_card.php     # Birthday card template
└── screenshot.png         # Theme screenshot
```

## Custom Post Types

### Birthday Cards (`aj_card`)
- Interactive card creation with custom content
- Scrapbook-style animations and effects
- Customizable backgrounds and images

### Popups (`aj_popup`)
- Custom popup management system
- White theme styling with smooth animations
- Trigger-based popup display

## Customizer Sections

- **Colors** - Complete color scheme customization
- **Hero** - Greeting, name, and subtitle settings
- **Portfolio Links** - Button management and contact info
- **Timeline** - Timeline sorting and display options
- **Footer** - Footer text customization

## CSS Variables

The theme uses CSS variables for easy customization:

```css
:root {
  --accent: #ff6b35;      /* Links, buttons, highlights */
  --bg: #ffffff;          /* Main background */
  --text: #1a1a1a;        /* Main text color */
  --muted: #666666;       /* Secondary text */
  --border: #e0e0e0;      /* Borders and dividers */
}
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## License

This theme is open source and available under the [MIT License](LICENSE).

## Development

Built with:
- **PHP** - WordPress theme development
- **JavaScript** - Interactive features and animations
- **CSS3** - Modern styling with CSS Grid and Flexbox
- **HTML5** - Semantic markup structure

---

**Theme Name:** Aleejaved Portfolio  
**Version:** 1.0.0  
**Author:** Alee Javed  
**Requires:** WordPress 5.0+  
**Tested up to:** WordPress 6.4+
