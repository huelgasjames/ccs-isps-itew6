import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(false)

  const toggleTheme = () => {
    console.log('toggleTheme called, current isDark:', isDark.value)
    isDark.value = !isDark.value
    console.log('toggleTheme new isDark:', isDark.value)
    updateTheme()
  }

  const setTheme = (dark: boolean) => {
    isDark.value = dark
    updateTheme()
  }

  const updateTheme = () => {
    const root = document.documentElement
    console.log('updateTheme called, isDark:', isDark.value)
    if (isDark.value) {
      root.classList.add('dark')
      localStorage.setItem('theme', 'dark')
      console.log('Added dark class to root')
    } else {
      root.classList.remove('dark')
      localStorage.setItem('theme', 'light')
      console.log('Removed dark class from root')
    }
    console.log('Root classes after update:', root.className)
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
