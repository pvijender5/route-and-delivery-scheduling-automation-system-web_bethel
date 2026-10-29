/**
 * Product Editor Module
 * Handles rich text editor initialization and management for product descriptions
 */
const ProductEditor = (function() {
    'use strict';

    // Private constants
    const EDITOR_CONFIG = {
        SNOW: {
            theme: 'snow',
            placeholder: 'Enter product description...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'font': [] }],
                    [{ 'align': [] }],
                    ['clean'],
                    ['link', 'image', 'video']
                ]
            }
        },
        BUBBLE: {
            theme: 'bubble',
            placeholder: 'Enter product description...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['link', 'image']
                ]
            }
        }
    };

    const SELECTORS = {
        MAIN_EDITOR: '#product-description-editor',
        SHORT_EDITOR: '#product-short-description-editor',
        SPECS_EDITOR: '#product-specifications-editor',
        FEATURES_EDITOR: '#product-features-editor'
    };

    // Private variables
    let editors = new Map();

    /**
     * Initialize a Quill editor instance
     * @private
     * @param {string} selector - Editor container selector
     * @param {Object} config - Editor configuration
     * @returns {Quill} Quill editor instance
     */
    const initializeEditor = (selector, config) => {
        try {
            const container = document.querySelector(selector);
            if (!container) {
                console.warn(`Editor container ${selector} not found`);
                return null;
            }

            const toolbarId = `${selector}-toolbar`;
            const toolbar = document.querySelector(toolbarId);
            
            const editorConfig = {
                ...config,
                modules: {
                    ...config.modules,
                    toolbar: toolbar || config.modules.toolbar
                }
            };

            const editor = new Quill(selector, editorConfig);
            
            // Add custom handlers
            editor.on('text-change', (delta, oldDelta, source) => {
                if (source === 'user') {
                    handleEditorChange(selector, editor);
                }
            });

            return editor;
        } catch (error) {
            console.error(`Error initializing editor for ${selector}:`, error);
            return null;
        }
    };

    /**
     * Handle editor content changes
     * @private
     * @param {string} selector - Editor selector
     * @param {Quill} editor - Quill editor instance
     */
    const handleEditorChange = (selector, editor) => {
        try {
            const content = editor.root.innerHTML;
            const plainText = editor.getText().trim();
            const hiddenInput = document.querySelector(`${selector}-input`);
            
            if (hiddenInput) {
                hiddenInput.value = content;
            }

            // Trigger custom event
            const event = new CustomEvent('editor-change', {
                detail: {
                    selector,
                    content,
                    plainText,
                    isEmpty: plainText.length === 0
                }
            });
            document.dispatchEvent(event);
        } catch (error) {
            console.error('Error handling editor change:', error);
        }
    };

    /**
     * Set editor content
     * @private
     * @param {string} selector - Editor selector
     * @param {string} content - HTML content
     */
    const setEditorContent = (selector, content) => {
        try {
            const editor = editors.get(selector);
            if (editor) {
                editor.root.innerHTML = content || '';
            }
        } catch (error) {
            console.error(`Error setting content for ${selector}:`, error);
        }
    };

    /**
     * Get editor content
     * @private
     * @param {string} selector - Editor selector
     * @returns {string} Editor content
     */
    const getEditorContent = (selector) => {
        try {
            const editor = editors.get(selector);
            return editor ? editor.root.innerHTML : '';
        } catch (error) {
            console.error(`Error getting content from ${selector}:`, error);
            return '';
        }
    };

    /**
     * Clear editor content
     * @private
     * @param {string} selector - Editor selector
     */
    const clearEditor = (selector) => {
        try {
            const editor = editors.get(selector);
            if (editor) {
                editor.setText('');
            }
        } catch (error) {
            console.error(`Error clearing editor ${selector}:`, error);
        }
    };

    /**
     * Initialize all editors
     * @private
     */
    const initializeAllEditors = () => {
        // Main description editor
        const mainEditor = initializeEditor(SELECTORS.MAIN_EDITOR, EDITOR_CONFIG.SNOW);
        if (mainEditor) {
            editors.set(SELECTORS.MAIN_EDITOR, mainEditor);
        }

        // Short description editor
        const shortEditor = initializeEditor(SELECTORS.SHORT_EDITOR, EDITOR_CONFIG.BUBBLE);
        if (shortEditor) {
            editors.set(SELECTORS.SHORT_EDITOR, shortEditor);
        }

        // Specifications editor
        const specsEditor = initializeEditor(SELECTORS.SPECS_EDITOR, EDITOR_CONFIG.SNOW);
        if (specsEditor) {
            editors.set(SELECTORS.SPECS_EDITOR, specsEditor);
        }

        // Features editor
        const featuresEditor = initializeEditor(SELECTORS.FEATURES_EDITOR, EDITOR_CONFIG.SNOW);
        if (featuresEditor) {
            editors.set(SELECTORS.FEATURES_EDITOR, featuresEditor);
        }
    };

    // Public API
    return {
        /**
         * Initialize the module
         * @public
         */
        init() {
            initializeAllEditors();
        },

        /**
         * Set content for a specific editor
         * @public
         * @param {string} selector - Editor selector
         * @param {string} content - HTML content
         */
        setContent(selector, content) {
            setEditorContent(selector, content);
        },

        /**
         * Get content from a specific editor
         * @public
         * @param {string} selector - Editor selector
         * @returns {string} Editor content
         */
        getContent(selector) {
            return getEditorContent(selector);
        },

        /**
         * Clear content of a specific editor
         * @public
         * @param {string} selector - Editor selector
         */
        clearContent(selector) {
            clearEditor(selector);
        },

        /**
         * Clear all editors
         * @public
         */
        clearAll() {
            Object.values(SELECTORS).forEach(selector => clearEditor(selector));
        },

        /**
         * Get all editor contents
         * @public
         * @returns {Object} Object containing all editor contents
         */
        getAllContents() {
            return Object.entries(SELECTORS).reduce((contents, [key, selector]) => {
                contents[key] = getEditorContent(selector);
                return contents;
            }, {});
        }
    };
})();

// Initialize on document ready
document.addEventListener('DOMContentLoaded', () => {
    ProductEditor.init();
});

// Export for global access
window.ProductEditor = ProductEditor;