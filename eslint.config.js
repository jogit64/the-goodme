import { defineConfig } from 'eslint/config'
import js from '@eslint/js'
import globals from 'globals'
import pluginVue from 'eslint-plugin-vue'
import prettier from 'eslint-plugin-prettier'

export default defineConfig([
  {
    files: ['**/*.{js,vue}'],
    languageOptions: {
      globals: globals.browser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
      },
    },
    plugins: {
      vue: pluginVue,
      prettier,
    },
    rules: {
      ...js.configs.recommended.rules,
      ...pluginVue.configs['flat/recommended'].rules,
      'prettier/prettier': 'warn',
      'vue/multi-word-component-names': 'off',
      'no-console': 'warn',
      'no-unused-vars': 'warn',
    },
  },
])
