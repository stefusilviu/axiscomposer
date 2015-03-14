/*!
 * jQuery AxisThemes Tooltip Core v1.0
 * Core Functionalities for Axis Tooltip
 *
 * Copyright 2013, AxisThemes
 * Freely distributable under the MIT license.
 */

( function ( $ ) {
	'use strict';

	$.AxisTooltip = function( options ) {
		var defaults = {
			delay: 1500,             // Delay in ms until the tooltip appears
			delayOut: 300,           // Delay in ms when instant showing should stop
			'class': 'axis-tooltip', // Tooltip classname for css styling and alignment
			data: 'axis-tooltip',    // Data Attribute that contains the tooltip text
			scope: 'body',           // Area of the tooltip should be applied to
			attach: 'element',       // Either attach the tooltip to the {element|body} // todo: implement mouse, make sure that it doesnt overlap with screen borders
			event: 'mouseenter',     // {mouseenter|click} and leave
			position: 'top'          // {top|bottom}
		};

		this.options = $.extend( {}, defaults, options );
		this.body    = $( 'body' );
		this.scope   = $( this.options.scope );
		this.tooltip = $( '<div class="' + this.options['class'] + ' tooltip-wrap"><span class="axis-arrow-wrap"><span class="axis-arrow"></span></span></div>' );
		this.inner   = $( '<div class="tooltip-inner"></div>' ).prependTo( this.tooltip );
		this.open    = false;
		this.timer   = false;
		this.active  = false;

		this.bind_events();
	};

	$.AxisTooltip.openTTs = [];
	$.AxisTooltip.prototype = {

		bind_events: function() {

			this.scope.on( this.options.event + ' mouseleave', '[data-' + this.options.data + ']', $.proxy( this.start_timer, this ) );

			if ( this.options.event !== 'click' ) {
				this.scope.on( 'mouseleave', '[data-' + this.options.data + ']', $.proxy( this.hide_tooltip, this ) );
			} else {
				this.body.on( 'mousedown', $.proxy( this.hide_tooltip, this ) );
			}
		},

		start_timer: function( e ) {

			// @todo: Implement this in future if builder necessary tooltip got working correctly :)
			// this.reset_timer;
			clearTimeout( this.timer );

			if ( e.type === this.options.event ) {
				var delay = this.options.event === 'click' ? 0 : this.open ? 0 : this.options.delay;
				this.timer = setTimeout( $.proxy( this.show_tooltip, this, e ), delay );
			} else if ( e.type === 'mouseleave' ) {
				this.timer = setTimeout( $.proxy( this.stop_instant_open, this, e ), this.options.delayOut );
			}

			e.preventDefault();
		},

		reset_timer: function() {
			clearTimeout( this.timer );
			this.timer = false;
		},

		stop_instant_open: function() {
			this.open = false;
		},

		show_tooltip: function( e ) {

			var target      = this.options.event === 'click' ? e.target : e.currentTarget,
				element     = $( target ),
				text        = element.data( this.options.data ).trim(),
				newTip      = element.data( 'axis-created-tooltip' ),
				attach      = this.options.attach === 'element' ? element : this.body,
				offset      = this.options.attach === 'element' ? element.position() : element.offset();

			// @todo: Implement this in future if builder necessary tooltip got working correctly :)
			// this.inner.html( text );
			// newTip = typeof newTip !== 'undefined' ? $.AxisTooltip.openTTs[newTip] : ( this.options.attach === 'element' ? this.tooltip.clone().insertAfter( attach ) : this.tooltip.clone().appendTo( attach ) );

			if ( typeof newTip !== 'undefined' ) {
				newTip = $.AxisTooltip.openTTs[newTip];
			} else {
				this.inner.html( text );
				newTip = this.options.attach === 'element' ? this.tooltip.clone().insertAfter( attach ) : this.tooltip.clone().appendTo( attach );
			}

			this.open = true;
			this.active = newTip;

			if ( newTip.is( ':animated:visible' ) && e.type === 'click' ) {
				return;
			}

			var css_top  = ( this.options.position === 'bottom' ) ? ( offset.top + element.outerHeight() ) : ( offset.top - newTip.outerHeight() ),
				css_left = ( offset.left + ( element.outerWidth() / 2 ) ) - ( newTip.outerWidth() / 2 );

			newTip.css({ opacity: 0, display: 'block', top: css_top - 10, left: css_left }).stop().animate({ top: css_top, opacity: 1 }, 200 );
			newTip.find( 'input[type=search]' ).focus();
			$.AxisTooltip.openTTs.push( newTip );
			element.data( 'axis-created-tooltip', $.AxisTooltip.openTTs.length - 1 );
		},

		hide_tooltip: function( e ) {

			var element = $( e.currentTarget ), newTip, animateTo;

			if ( this.options.event === 'click' ) {
				element = $( e.target );
			}

			if ( ! element.is( '.' + this.options['class'] ) && element.parents( '.' + this.options['class'] ).length === 0 ) {
				if ( this.active.length ) {
					newTip = this.active;
					this.active = false;
				}
			} else {
				newTip = element.data( 'axis-created-tooltip' );
				newTip = typeof newTip !== 'undefined' ? $.AxisTooltip.openTTs[newTip] : false;
			}

			if ( newTip ) {
				animateTo = ( parseInt( newTip.css( 'top' ), 10 ) - 10 );
				newTip.animate({ top: animateTo, opacity: 0 }, 200, function() {
					newTip.css({ display: 'none' });
				});
			}
		}
    };

})( jQuery );
