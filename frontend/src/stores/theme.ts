import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(false)

  const toggleTheme = () => {
    console.log('=== TOGGLE THEME START ===')
    console.log('Current isDark:', isDark.value)
    isDark.value = !isDark.value
    console.log('New isDark:', isDark.value)
    updateTheme()
    console.log('=== TOGGLE THEME END ===')
  }

  const setTheme = (dark: boolean) => {
    isDark.value = dark
    updateTheme()
  }

  const updateTheme = () => {
    const root = document.documentElement
    console.log('UPDATE THEME - isDark:', isDark.value)
    console.log('UPDATE THEME - root element:', root)
    console.log('UPDATE THEME - current classes:', root.className)
    
    if (isDark.value) {
      root.classList.add('dark')
      localStorage.setItem('theme', 'dark')
      console.log('UPDATE THEME - Added dark class')
    } else {
      root.classList.remove('dark')
      localStorage.setItem('theme', 'light')
      console.log('UPDATE THEME - Removed dark class')
    }
    
    console.log('UPDATE THEME - final classes:', root.className)
    console.log('UPDATE THEME - computed style:', getComputedStyle(root).getPropertyValue('--login-bg'))
  }

  const initTheme = () => {
    const savedTheme = localStorage.getItem('theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    isDark.value = savedTheme === 'dark' || (!savedTheme && prefersDark)
    updateTheme()
  }

  return {
    isDark,
    toggleTheme,
    setTheme,
    initTheme
  }
})
