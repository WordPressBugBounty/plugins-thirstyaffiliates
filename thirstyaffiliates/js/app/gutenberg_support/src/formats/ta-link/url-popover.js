const { __ } = wp.i18n;
const { useState } = wp.element;
const { Popover , Button } = wp.components;
const { useAnchor } = wp.richText;
const { getRectangleFromRange } = wp.dom;
let lastAnchorRect = null;

/**
 * Custom URL Popover component.
 *
 * @since 3.6
 */
function ThirstyURLPopover ( {
    children,
    renderSettings,
    addingLink,
    invalidLink,
    position = 'bottom center',
    focusOnMount = 'firstElement',
    value,
    contentRef,
    ...popoverProps
} ) {
    /**
     * Get selected anchor text.
     *
     * Helper method for WP <6.1.
     *
     * @since 3.9
     */
    function getAnchorRect() {
        const selection = window.getSelection();
        const range = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;

        if ( ! range ) {
            return;
        }

        // Fix for the link UI jumping around the screen on WP 5.6
        if(selection.anchorNode.nodeType !== window.Node.TEXT_NODE) {
            return lastAnchorRect;
        }

        let rect = null;

        if ( addingLink ) {
            rect = getRectangleFromRange( range );
        } else {

            let element = range.startContainer;

            // If the caret is right before the element, select the next element.
            element = element.nextElementSibling || element;

            while ( element.nodeType !== window.Node.ELEMENT_NODE ) {
                element = element.parentNode;
            }

            const closest = element.closest( 'ta' );
            if ( closest ) {
                rect = closest.getBoundingClientRect();
            }

        }

        lastAnchorRect = rect;

        return rect;
    }

    const [ isSettingsExpanded, setIsSettingsExpanded ] = useState( false );
    const showSettings = !! renderSettings && isSettingsExpanded();
    let anchorProps;

    if ( useAnchor ) {
        // Prefer useAnchor over useAnchorRef, this is WP 6.1+ only
        anchorProps = {
            anchor: useAnchor( {
                editableContentElement: contentRef.current,
                value,
                settings: {
                    tagName: 'ta'
                },
            } )
        };
    } else {
        anchorProps = {
            anchorRect: getAnchorRect(),
        };
    }

    return (
        <Popover
            className="ta-url-popover editor-url-popover block-editor-url-popover"
            focusOnMount={ focusOnMount }
            position={ position }
            { ...anchorProps }
            { ...popoverProps }
        >
            <div className="editor-url-popover__row">
                { children }
                { !! renderSettings && (
                    <Button
                        className="editor-url-popover__settings-toggle"
                        icon="ellipsis"
                        label={ __( 'Link Settings' ) }
                        onClick={ () => { setIsSettingsExpanded( !isSettingsExpanded() ) } }
                        aria-expanded={ isSettingsExpanded() }
                    />
                ) }
            </div>
            { showSettings && (
                <div className="editor-url-popover__row editor-url-popover__settings">
                    { renderSettings() }
                </div>
            ) }
            { invalidLink && <div className="ta-invalid-link">{ __( 'Invalid affiliate link' ) }</div> }
        </Popover>
    );
}

export default ThirstyURLPopover;
