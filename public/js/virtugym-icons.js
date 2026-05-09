(function () {
    const iconMap = new Map([
        ['📊', 'chart-no-axes-combined'],
        ['👥', 'users'],
        ['🏋️', 'dumbbell'],
        ['🏋', 'dumbbell'],
        ['📅', 'calendar-days'],
        ['💰', 'wallet'],
        ['💸', 'banknote'],
        ['📈', 'trending-up'],
        ['💪', 'activity'],
        ['🎯', 'target'],
        ['💬', 'message-circle'],
        ['🤖', 'bot'],
        ['⏰', 'clock'],
        ['⏱️', 'timer'],
        ['⏱', 'timer'],
        ['🔥', 'flame'],
        ['✅', 'circle-check'],
        ['❌', 'circle-x'],
        ['⚙️', 'settings'],
        ['⚙', 'settings'],
        ['🚪', 'log-out'],
        ['⭐', 'star'],
        ['⚠️', 'triangle-alert'],
        ['⚠', 'triangle-alert'],
        ['🔄', 'refresh-cw'],
        ['⚡', 'zap'],
        ['💡', 'lightbulb'],
        ['📝', 'file-text'],
        ['👍', 'thumbs-up'],
        ['🍽️', 'utensils'],
        ['🍽', 'utensils'],
        ['🥩', 'beef'],
        ['🍚', 'wheat'],
        ['🥑', 'apple'],
        ['🕐', 'clock-1'],
        ['🕒', 'clock-3'],
        ['🗑️', 'trash-2'],
        ['🗑', 'trash-2'],
        ['🔒', 'lock'],
        ['🎥', 'video'],
        ['🎉', 'party-popper'],
        ['🏷️', 'tag'],
        ['🏷', 'tag'],
        ['⚖️', 'scale'],
        ['⚖', 'scale'],
        ['🔝', 'arrow-up-to-line'],
        ['🏃', 'footprints'],
        ['⚫', 'circle']
    ]);

    const emojiPattern = new RegExp(Array.from(iconMap.keys()).map(escapeRegExp).join('|'), 'gu');
    const skippedTags = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'INPUT', 'OPTION', 'SELECT', 'SVG']);

    function escapeRegExp(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function makeIcon(name) {
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', name);
        icon.className = 'vg-inline-icon';
        icon.setAttribute('aria-hidden', 'true');
        return icon;
    }

    function replaceTextNode(node) {
        const text = node.nodeValue;
        emojiPattern.lastIndex = 0;
        if (!emojiPattern.test(text)) {
            return;
        }

        emojiPattern.lastIndex = 0;
        const fragment = document.createDocumentFragment();
        let cursor = 0;
        let match;

        while ((match = emojiPattern.exec(text)) !== null) {
            if (match.index > cursor) {
                fragment.appendChild(document.createTextNode(text.slice(cursor, match.index)));
            }

            fragment.appendChild(makeIcon(iconMap.get(match[0])));
            cursor = match.index + match[0].length;
        }

        if (cursor < text.length) {
            fragment.appendChild(document.createTextNode(text.slice(cursor)));
        }

        node.parentNode.replaceChild(fragment, node);
    }

    function stripOptionEmoji(root) {
        root.querySelectorAll('option').forEach(function (option) {
            emojiPattern.lastIndex = 0;
            option.textContent = option.textContent.replace(emojiPattern, '').replace(/\s+/g, ' ').trim();
        });
    }

    function replaceEmojiIcons(root) {
        const scope = root || document.body;
        if (!scope || skippedTags.has(scope.tagName.toUpperCase())) {
            return;
        }

        stripOptionEmoji(scope.nodeType === Node.ELEMENT_NODE ? scope : document);

        const walker = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                const parent = node.parentElement;
                if (!parent || skippedTags.has(parent.tagName.toUpperCase()) || parent.closest('[data-icons-processed="true"]')) {
                    return NodeFilter.FILTER_REJECT;
                }
                emojiPattern.lastIndex = 0;
                return emojiPattern.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            }
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach(replaceTextNode);

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function observeDynamicContent() {
        const observer = new MutationObserver(function (mutations) {
            // Disconnect to prevent infinite loops from our own DOM modifications
            observer.disconnect();

            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        replaceEmojiIcons(node);
                    } else if (node.nodeType === Node.TEXT_NODE && node.parentNode) {
                        replaceTextNode(node);
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }
                });
            });

            // Reconnect after processing
            observer.observe(document.body, { childList: true, subtree: true });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    window.VirtuGymIcons = {
        refresh: replaceEmojiIcons
    };

    document.addEventListener('DOMContentLoaded', function () {
        replaceEmojiIcons(document.body);
        observeDynamicContent();
    });
})();
