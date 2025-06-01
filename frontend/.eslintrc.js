module.exports = {
  root: true,
  env: {
    node: true,
    es2021: true, // Using a recent ES version
    'vue/setup-compiler-macros': true // Enables defineProps, defineEmits, etc.
  },
  extends: [
    'plugin:vue/vue3-essential', // Base Vue 3 rules
    // Consider 'plugin:vue/vue3-recommended' for stricter common practices
    'eslint:recommended'      // Basic ESLint recommended rules
  ],
  parserOptions: {
    ecmaVersion: 'latest', // Use the latest ECMAScript standard
    sourceType: 'module'
  },
  rules: {
    // Basic rules to prevent common issues or enforce consistency
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',

    // Vue specific rules (examples, can be customized)
    // 'vue/no-unused-vars': 'warn', // Warns about unused variables in <template>
    'vue/multi-word-component-names': 'off', // Allows single-word component names like App.vue, LoginForm.vue
    'vue/no-reserved-component-names': 'warn',
    'vue/no-mutating-props': 'warn', // Good practice to avoid mutating props directly

    // General JavaScript rules
    'no-unused-vars': ['warn', { 'argsIgnorePattern': '^_' }], // Warn on unused JS variables, ignore if prefixed with _
    'semi': ['warn', 'always'], // Enforce semicolons
    'quotes': ['warn', 'single', { 'avoidEscape': true, 'allowTemplateLiterals': true }], // Enforce single quotes
    'indent': ['warn', 2], // Enforce 2-space indentation
    'comma-dangle': ['warn', 'always-multiline'], // Require trailing commas in multiline objects/arrays
    'object-curly-spacing': ['warn', 'always'], // Enforce spacing inside curly braces
    'arrow-spacing': ['warn', { 'before': true, 'after': true }] // Enforce spacing around arrow function arrows
  }
};
