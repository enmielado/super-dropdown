module.exports = {
  plugins: ['@vuepress/back-to-top'],
  title: 'Super Dropdown Documentation',
  description: 'Git it...',
  base: '/plugins/superdropdown/docs/',
  dest: '../../../veryfine.work/web/plugins/superdropdown/docs/',
  themeConfig: {
    logo: 'https://veryfine.work/assets/img/veryfine.png',
    smoothScroll: true,
    displayAllHeaders: true, // show headers for all pages
    sidebarDepth: 3,
    sidebar: 'auto',
    // sidebar: [
    //   ['/', 'Retour plugin for Craft CMS 3.x'],
    //   ['/subpage', 'Subpage']
    // ],
    nav: [
      { text: 'Super Dropdown', link: 'https://veryfine.work/plugins/superdropdown' },
      { text: 'Very Fine Plugins', link: 'https://veryfine.work/plugins' },
      { text: 'GitHub', link: 'https://github.com/veryfinework/super-dropdown' }
    ]
  }
}
