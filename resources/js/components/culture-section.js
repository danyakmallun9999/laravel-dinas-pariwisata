export default () => ({
    active: 0,
    shown: false,

    init() {
        // Initialize if needed
    },

    setActive(index) {
        this.active = index;
    },

    reveal() {
        this.shown = true;
    },

    // Computed classes helpers could go here if logic gets complex, 
    // but for now keeping it simple as per original design.
})
