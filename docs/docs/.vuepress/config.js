module.exports = {
  plugins: ['@vuepress/back-to-top'],
  title: 'Super Dropdown Documentation',
  description: 'Complete docs for the Super Dropdown for Craft CMS',
  base: '/plugins/superdropdown/docs/',
  dest: '../../../veryfine.work/web/plugins/superdropdown/docs/',
  themeConfig: {
    logo: 'https://veryfine.work/assets/img/veryfine.png',
    smoothScroll: true,
    displayAllHeaders: true, // show headers for all pages
    sidebarDepth: 3,
    sidebar: 'auto',
    nav: [
      { text: 'GitHub', link: 'https://github.com/veryfinework/super-dropdown' }
    ]
  }
}
