const postcss = require('postcss');
const fs = require('fs');

const cssPath = './resources/css/app.css';
const css = fs.readFileSync(cssPath, 'utf8');

const plugin = (root) => {
    // Step 1: Split comma-separated rules
    root.walkRules((rule) => {
        if (rule.selectors.length > 1) {
            if (rule.parent && rule.parent.type === 'atrule' && rule.parent.name === 'keyframes') return;
            rule.selectors.forEach(selector => {
                const clonedRule = rule.clone({ selectors: [selector] });
                rule.parent.insertBefore(rule, clonedRule);
            });
            rule.remove();
        }
    });

    // Step 2: Combine identical selectors sequentially
    root.walk((node) => {
        if (node.type === 'root' || node.type === 'atrule') {
            const seenRules = new Map();
            if (node.nodes) {
                const children = [...node.nodes];
                children.forEach((child) => {
                    if (child.type === 'rule') {
                        const selector = child.selector;
                        if (seenRules.has(selector)) {
                            const targetRule = seenRules.get(selector);
                            child.walkDecls((decl) => {
                                targetRule.append(decl.clone());
                            });
                            child.remove();
                        } else {
                            seenRules.set(selector, child);
                        }
                    }
                });
            }
        }
    });
};

postcss([plugin]).process(css, { from: cssPath, to: cssPath }).then(result => {
    fs.writeFileSync(cssPath, result.css);
    console.log('CSS refactored successfully!');
});
