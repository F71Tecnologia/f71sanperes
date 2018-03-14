// ********************** //
// Spry Collapsible Panel //
// ********************** //

var Spry;
if (!Spry) Spry = {};
if (!Spry.Widget) Spry.Widget = {};

Spry.Widget.CollapsiblePanel = function(element, opts)
{
	this.init(element);

	Spry.Widget.CollapsiblePanel.setOptions(this, opts);

	this.attachBehaviors();
};

Spry.Widget.CollapsiblePanel.prototype.init = function(element)
{
	this.element = this.getElement(element);
	this.focusElement = null;
	this.hoverClass = "CollapsiblePanelTabHover";
	this.openClass = "CollapsiblePanelOpen";
	this.closedClass = "CollapsiblePanelClosed";
	this.focusedClass = "CollapsiblePanelFocused";
	this.enableAnimation = true;
	this.enableKeyboardNavigation = true;
	this.animator = null;
	this.hasFocus = false;
	this.contentIsOpen = true;
};

Spry.Widget.CollapsiblePanel.prototype.getElement = function(ele)
{
	if (ele && typeof ele == "string")
		return document.getElementById(ele);
	return ele;
};

Spry.Widget.CollapsiblePanel.prototype.addClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) != -1))
		return;
	ele.className += (ele.className ? " " : "") + className;
};

Spry.Widget.CollapsiblePanel.prototype.removeClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) == -1))
		return;
	ele.className = ele.className.replace(new RegExp("\\s*\\b" + className + "\\b", "g"), "");
};

Spry.Widget.CollapsiblePanel.prototype.hasClassName = function(ele, className)
{
	if (!ele || !className || !ele.className || ele.className.search(new RegExp("\\b" + className + "\\b")) == -1)
		return false;
	return true;
};

Spry.Widget.CollapsiblePanel.prototype.setDisplay = function(ele, display)
{
	if( ele )
		ele.style.display = display;
};

Spry.Widget.CollapsiblePanel.setOptions = function(obj, optionsObj, ignoreUndefinedProps)
{
	if (!optionsObj)
		return;
	for (var optionName in optionsObj)
	{
		if (ignoreUndefinedProps && optionsObj[optionName] == undefined)
			continue;
		obj[optionName] = optionsObj[optionName];
	}
};

Spry.Widget.CollapsiblePanel.prototype.onTabMouseOver = function()
{
	this.addClassName(this.getTab(), this.hoverClass);
};

Spry.Widget.CollapsiblePanel.prototype.onTabMouseOut = function()
{
	this.removeClassName(this.getTab(), this.hoverClass);
};

Spry.Widget.CollapsiblePanel.prototype.open = function()
{
	this.contentIsOpen = true;
	if (this.enableAnimation)
	{
		if (this.animator)
			this.animator.stop();
		this.animator = new Spry.Widget.CollapsiblePanel.PanelAnimator(this, true);
		this.animator.start();
	}
	else
		this.setDisplay(this.getContent(), "block");

	this.removeClassName(this.element, this.closedClass);
	this.addClassName(this.element, this.openClass);
};

Spry.Widget.CollapsiblePanel.prototype.close = function()
{
	this.contentIsOpen = false;
	if (this.enableAnimation)
	{
		if (this.animator)
			this.animator.stop();
		this.animator = new Spry.Widget.CollapsiblePanel.PanelAnimator(this, false);
		this.animator.start();
	}
	else
		this.setDisplay(this.getContent(), "none");

	this.removeClassName(this.element, this.openClass);
	this.addClassName(this.element, this.closedClass);
};

Spry.Widget.CollapsiblePanel.prototype.onTabClick = function()
{
	if (this.isOpen())
		this.close();
	else
		this.open();
	this.focus();
};

Spry.Widget.CollapsiblePanel.prototype.onFocus = function(e)
{
	this.hasFocus = true;
	this.addClassName(this.element, this.focusedClass);
};

Spry.Widget.CollapsiblePanel.prototype.onBlur = function(e)
{
	this.hasFocus = false;
	this.removeClassName(this.element, this.focusedClass);
};

Spry.Widget.CollapsiblePanel.ENTER_KEY = 13;
Spry.Widget.CollapsiblePanel.SPACE_KEY = 32;

Spry.Widget.CollapsiblePanel.prototype.onKeyDown = function(e)
{
	var key = e.keyCode;
	if (!this.hasFocus || (key != Spry.Widget.CollapsiblePanel.ENTER_KEY && key != Spry.Widget.CollapsiblePanel.SPACE_KEY))
		return true;
	
	if (this.isOpen())
		this.close();
	else
		this.open();

	if (e.stopPropagation)
		e.stopPropagation();
	if (e.preventDefault)
		e.preventDefault();

	return false;
};

Spry.Widget.CollapsiblePanel.prototype.attachPanelHandlers = function()
{
	var tab = this.getTab();
	if (!tab)
		return;

	var self = this;
	Spry.Widget.CollapsiblePanel.addEventListener(tab, "click", function(e) { return self.onTabClick(); }, false);
	Spry.Widget.CollapsiblePanel.addEventListener(tab, "mouseover", function(e) { return self.onTabMouseOver(); }, false);
	Spry.Widget.CollapsiblePanel.addEventListener(tab, "mouseout", function(e) { return self.onTabMouseOut(); }, false);

	if (this.enableKeyboardNavigation)
	{
		// XXX: IE doesn't allow the setting of tabindex dynamically. This means we can't
		// rely on adding the tabindex attribute if it is missing to enable keyboard navigation
		// by default.

		// Find the first element within the tab container that has a tabindex or the first
		// anchor tag.
		
		var tabIndexEle = null;
		var tabAnchorEle = null;

		this.preorderTraversal(tab, function(node) {
			if (node.nodeType == 1 /* NODE.ELEMENT_NODE */)
			{
				var tabIndexAttr = tab.attributes.getNamedItem("tabindex");
				if (tabIndexAttr)
				{
					tabIndexEle = node;
					return true;
				}
				if (!tabAnchorEle && node.nodeName.toLowerCase() == "a")
					tabAnchorEle = node;
			}
			return false;
		});

		if (tabIndexEle)
			this.focusElement = tabIndexEle;
		else if (tabAnchorEle)
			this.focusElement = tabAnchorEle;

		if (this.focusElement)
		{
			Spry.Widget.CollapsiblePanel.addEventListener(this.focusElement, "focus", function(e) { return self.onFocus(e); }, false);
			Spry.Widget.CollapsiblePanel.addEventListener(this.focusElement, "blur", function(e) { return self.onBlur(e); }, false);
			Spry.Widget.CollapsiblePanel.addEventListener(this.focusElement, "keydown", function(e) { return self.onKeyDown(e); }, false);
		}
	}
};

Spry.Widget.CollapsiblePanel.addEventListener = function(element, eventType, handler, capture)
{
	try
	{
		if (element.addEventListener)
			element.addEventListener(eventType, handler, capture);
		else if (element.attachEvent)
			element.attachEvent("on" + eventType, handler);
	}
	catch (e) {}
};

Spry.Widget.CollapsiblePanel.prototype.preorderTraversal = function(root, func)
{
	var stopTraversal = false;
	if (root)
	{
		stopTraversal = func(root);
		if (root.hasChildNodes())
		{
			var child = root.firstChild;
			while (!stopTraversal && child)
			{
				stopTraversal = this.preorderTraversal(child, func);
				try { child = child.nextSibling; } catch (e) { child = null; }
			}
		}
	}
	return stopTraversal;
};

Spry.Widget.CollapsiblePanel.prototype.attachBehaviors = function()
{
	var panel = this.element;
	var tab = this.getTab();
	var content = this.getContent();

	if (this.contentIsOpen || this.hasClassName(panel, this.openClass))
	{
		this.removeClassName(panel, this.closedClass);
		this.setDisplay(content, "block");
		this.contentIsOpen = true;
	}
	else
	{
		this.removeClassName(panel, this.openClass);
		this.addClassName(panel, this.closedClass);
		this.setDisplay(content, "none");
		this.contentIsOpen = false;
	}

	this.attachPanelHandlers();
};

Spry.Widget.CollapsiblePanel.prototype.getTab = function()
{
	return this.getElementChildren(this.element)[0];
};

Spry.Widget.CollapsiblePanel.prototype.getContent = function()
{
	return this.getElementChildren(this.element)[1];
};

Spry.Widget.CollapsiblePanel.prototype.isOpen = function()
{
	return this.contentIsOpen;
};

Spry.Widget.CollapsiblePanel.prototype.getElementChildren = function(element)
{
	var children = [];
	var child = element.firstChild;
	while (child)
	{
		if (child.nodeType == 1 /* Node.ELEMENT_NODE */)
			children.push(child);
		child = child.nextSibling;
	}
	return children;
};

Spry.Widget.CollapsiblePanel.prototype.focus = function()
{
	if (this.focusElement && this.focusElement.focus)
		this.focusElement.focus();
};

/////////////////////////////////////////////////////

Spry.Widget.CollapsiblePanel.PanelAnimator = function(panel, doOpen, opts)
{
	this.timer = null;
	this.interval = 0;
	this.stepCount = 0;

	this.fps = 0;
	this.steps = 10;
	this.duration = 500;
	this.onComplete = null;

	this.panel = panel;
	this.content = panel.getContent();
	this.panelData = [];
	this.doOpen = doOpen;

	Spry.Widget.CollapsiblePanel.setOptions(this, opts);


	// If caller specified speed in terms of frames per second,
	// convert them into steps.

	if (this.fps > 0)
	{
		this.interval = Math.floor(1000 / this.fps);
		this.steps = parseInt((this.duration + (this.interval - 1)) / this.interval);
	}
	else if (this.steps > 0)
		this.interval = this.duration / this.steps;

	var c = this.content;

	var curHeight = c.offsetHeight ? c.offsetHeight : 0;
	
	if (doOpen && c.style.display == "none")
		this.fromHeight = 0;
	else
		this.fromHeight = curHeight;

	if (!doOpen)
		this.toHeight = 0;
	else
	{
		if (c.style.display == "none")
		{
			// The content area is not displayed so in order to calculate the extent
			// of the content inside it, we have to set its display to block.

			c.style.visibility = "hidden";
			c.style.display = "block";
		}

		// Unfortunately in Mozilla/Firefox, fetching the offsetHeight seems to cause
		// the browser to synchronously re-layout and re-display content on the page,
		// so we see a brief flash of content that is *after* the panel being positioned
		// where it should when the panel is fully expanded. To get around this, we
		// temporarily position the content area of the panel absolutely off-screen.
		// This has the effect of taking the content out-of-flow, so nothing shifts around.

		// var oldPos = c.style.position;
		// var oldLeft = c.style.left;
		// c.style.position = "absolute";
		// c.style.left = "-2000em";

		// Clear the height property so we can calculate
		// the full height of the content we are going to show.
		c.style.height = "";
		this.toHeight = c.offsetHeight;

		// Now restore the position and offset to what it was!
		// c.style.position = oldPos;
		// c.style.left = oldLeft;
	}

	this.increment = (this.toHeight - this.fromHeight) / this.steps;
	this.overflow = c.style.overflow;

	c.style.height = this.fromHeight + "px";
	c.style.visibility = "visible";
	c.style.overflow = "hidden";
	c.style.display = "block";
};

Spry.Widget.CollapsiblePanel.PanelAnimator.prototype.start = function()
{
	var self = this;
	this.timer = setTimeout(function() { self.stepAnimation(); }, this.interval);
};

Spry.Widget.CollapsiblePanel.PanelAnimator.prototype.stop = function()
{
	if (this.timer)
	{
		clearTimeout(this.timer);

		// If we're killing the timer, restore the overflow
		// properties on the panels we were animating!

		if (this.stepCount < this.steps)
			this.content.style.overflow = this.overflow;
	}

	this.timer = null;
};

Spry.Widget.CollapsiblePanel.PanelAnimator.prototype.stepAnimation = function()
{
	++this.stepCount;

	this.animate();

	if (this.stepCount < this.steps)
		this.start();
	else if (this.onComplete)
		this.onComplete();
};

Spry.Widget.CollapsiblePanel.PanelAnimator.prototype.animate = function()
{
	if (this.stepCount >= this.steps)
	{
		if (!this.doOpen)
			this.content.style.display = "none";
		this.content.style.overflow = this.overflow;
		this.content.style.height = this.toHeight + "px";
	}
	else
	{
		this.fromHeight += this.increment;
		this.content.style.height = this.fromHeight + "px";
	}
};



// ****************** //
// Spry Tabbed Panels //
// ****************** //



var Spry;
if (!Spry) Spry = {};
if (!Spry.Widget) Spry.Widget = {};

Spry.Widget.TabbedPanels = function(element, opts)
{
	this.element = this.getElement(element);
	this.defaultTab = 0; // Show the first panel by default.
	this.bindings = [];
	this.tabSelectedClass = "TabbedPanelsTabSelected";
	this.tabHoverClass = "TabbedPanelsTabHover";
	this.tabFocusedClass = "TabbedPanelsTabFocused";
	this.panelVisibleClass = "TabbedPanelsContentVisible";
	this.focusElement = null;
	this.hasFocus = false;
	this.currentTabIndex = 0;
	this.enableKeyboardNavigation = true;

	Spry.Widget.TabbedPanels.setOptions(this, opts);

	// If the defaultTab is expressed as a number/index, convert
	// it to an element.

	if (typeof (this.defaultTab) == "number")
	{
		if (this.defaultTab < 0)
			this.defaultTab = 0;
		else
		{
			var count = this.getTabbedPanelCount();
			if (this.defaultTab >= count)
				this.defaultTab = (count > 1) ? (count - 1) : 0;
		}

		this.defaultTab = this.getTabs()[this.defaultTab];
	}

	// The defaultTab property is supposed to be the tab element for the tab content
	// to show by default. The caller is allowed to pass in the element itself or the
	// element's id, so we need to convert the current value to an element if necessary.

	if (this.defaultTab)
		this.defaultTab = this.getElement(this.defaultTab);

	this.attachBehaviors();
};

Spry.Widget.TabbedPanels.prototype.getElement = function(ele)
{
	if (ele && typeof ele == "string")
		return document.getElementById(ele);
	return ele;
}

Spry.Widget.TabbedPanels.prototype.getElementChildren = function(element)
{
	var children = [];
	var child = element.firstChild;
	while (child)
	{
		if (child.nodeType == 1 /* Node.ELEMENT_NODE */)
			children.push(child);
		child = child.nextSibling;
	}
	return children;
};

Spry.Widget.TabbedPanels.prototype.addClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) != -1))
		return;
	ele.className += (ele.className ? " " : "") + className;
};

Spry.Widget.TabbedPanels.prototype.removeClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) == -1))
		return;
	ele.className = ele.className.replace(new RegExp("\\s*\\b" + className + "\\b", "g"), "");
};

Spry.Widget.TabbedPanels.setOptions = function(obj, optionsObj, ignoreUndefinedProps)
{
	if (!optionsObj)
		return;
	for (var optionName in optionsObj)
	{
		if (ignoreUndefinedProps && optionsObj[optionName] == undefined)
			continue;
		obj[optionName] = optionsObj[optionName];
	}
};

Spry.Widget.TabbedPanels.prototype.getTabGroup = function()
{
	if (this.element)
	{
		var children = this.getElementChildren(this.element);
		if (children.length)
			return children[0];
	}
	return null;
};

Spry.Widget.TabbedPanels.prototype.getTabs = function()
{
	var tabs = [];
	var tg = this.getTabGroup();
	if (tg)
		tabs = this.getElementChildren(tg);
	return tabs;
};

Spry.Widget.TabbedPanels.prototype.getContentPanelGroup = function()
{
	if (this.element)
	{
		var children = this.getElementChildren(this.element);
		if (children.length > 1)
			return children[1];
	}
	return null;
};

Spry.Widget.TabbedPanels.prototype.getContentPanels = function()
{
	var panels = [];
	var pg = this.getContentPanelGroup();
	if (pg)
		panels = this.getElementChildren(pg);
	return panels;
};

Spry.Widget.TabbedPanels.prototype.getIndex = function(ele, arr)
{
	ele = this.getElement(ele);
	if (ele && arr && arr.length)
	{
		for (var i = 0; i < arr.length; i++)
		{
			if (ele == arr[i])
				return i;
		}
	}
	return -1;
};

Spry.Widget.TabbedPanels.prototype.getTabIndex = function(ele)
{
	var i = this.getIndex(ele, this.getTabs());
	if (i < 0)
		i = this.getIndex(ele, this.getContentPanels());
	return i;
};

Spry.Widget.TabbedPanels.prototype.getCurrentTabIndex = function()
{
	return this.currentTabIndex;
};

Spry.Widget.TabbedPanels.prototype.getTabbedPanelCount = function(ele)
{
	return Math.min(this.getTabs().length, this.getContentPanels().length);
};

Spry.Widget.TabbedPanels.addEventListener = function(element, eventType, handler, capture)
{
	try

	{
		if (element.addEventListener)
			element.addEventListener(eventType, handler, capture);
		else if (element.attachEvent)
			element.attachEvent("on" + eventType, handler);
	}
	catch (e) {}
};

Spry.Widget.TabbedPanels.prototype.onTabClick = function(e, tab)
{
	this.showPanel(tab);
};

Spry.Widget.TabbedPanels.prototype.onTabMouseOver = function(e, tab)
{
	this.addClassName(tab, this.tabHoverClass);
};

Spry.Widget.TabbedPanels.prototype.onTabMouseOut = function(e, tab)
{
	this.removeClassName(tab, this.tabHoverClass);
};

Spry.Widget.TabbedPanels.prototype.onTabFocus = function(e, tab)
{
	this.hasFocus = true;
	this.addClassName(this.element, this.tabFocusedClass);
};

Spry.Widget.TabbedPanels.prototype.onTabBlur = function(e, tab)
{
	this.hasFocus = false;
	this.removeClassName(this.element, this.tabFocusedClass);
};

Spry.Widget.TabbedPanels.ENTER_KEY = 13;
Spry.Widget.TabbedPanels.SPACE_KEY = 32;

Spry.Widget.TabbedPanels.prototype.onTabKeyDown = function(e, tab)
{
	var key = e.keyCode;
	if (!this.hasFocus || (key != Spry.Widget.TabbedPanels.ENTER_KEY && key != Spry.Widget.TabbedPanels.SPACE_KEY))
		return true;

	this.showPanel(tab);

	if (e.stopPropagation)
		e.stopPropagation();
	if (e.preventDefault)
		e.preventDefault();

	return false;
};

Spry.Widget.TabbedPanels.prototype.preorderTraversal = function(root, func)
{
	var stopTraversal = false;
	if (root)
	{
		stopTraversal = func(root);
		if (root.hasChildNodes())
		{
			var child = root.firstChild;
			while (!stopTraversal && child)
			{
				stopTraversal = this.preorderTraversal(child, func);
				try { child = child.nextSibling; } catch (e) { child = null; }
			}
		}
	}
	return stopTraversal;
};

Spry.Widget.TabbedPanels.prototype.addPanelEventListeners = function(tab, panel)
{
	var self = this;
	Spry.Widget.TabbedPanels.addEventListener(tab, "click", function(e) { return self.onTabClick(e, tab); }, false);
	Spry.Widget.TabbedPanels.addEventListener(tab, "mouseover", function(e) { return self.onTabMouseOver(e, tab); }, false);
	Spry.Widget.TabbedPanels.addEventListener(tab, "mouseout", function(e) { return self.onTabMouseOut(e, tab); }, false);

	if (this.enableKeyboardNavigation)
	{
		// XXX: IE doesn't allow the setting of tabindex dynamically. This means we can't
		// rely on adding the tabindex attribute if it is missing to enable keyboard navigation
		// by default.

		// Find the first element within the tab container that has a tabindex or the first
		// anchor tag.
		
		var tabIndexEle = null;
		var tabAnchorEle = null;

		this.preorderTraversal(tab, function(node) {
			if (node.nodeType == 1 /* NODE.ELEMENT_NODE */)
			{
				var tabIndexAttr = tab.attributes.getNamedItem("tabindex");
				if (tabIndexAttr)
				{
					tabIndexEle = node;
					return true;
				}
				if (!tabAnchorEle && node.nodeName.toLowerCase() == "a")
					tabAnchorEle = node;
			}
			return false;
		});

		if (tabIndexEle)
			this.focusElement = tabIndexEle;
		else if (tabAnchorEle)
			this.focusElement = tabAnchorEle;

		if (this.focusElement)
		{
			Spry.Widget.TabbedPanels.addEventListener(this.focusElement, "focus", function(e) { return self.onTabFocus(e, tab); }, false);
			Spry.Widget.TabbedPanels.addEventListener(this.focusElement, "blur", function(e) { return self.onTabBlur(e, tab); }, false);
			Spry.Widget.TabbedPanels.addEventListener(this.focusElement, "keydown", function(e) { return self.onTabKeyDown(e, tab); }, false);
		}
	}
};

Spry.Widget.TabbedPanels.prototype.showPanel = function(elementOrIndex)
{
	var tpIndex = -1;
	
	if (typeof elementOrIndex == "number")
		tpIndex = elementOrIndex;

	else // Must be the element for the tab or content panel.
		tpIndex = this.getTabIndex(elementOrIndex);
	
	if (!tpIndex < 0 || tpIndex >= this.getTabbedPanelCount())
		return;

	var tabs = this.getTabs();
	var panels = this.getContentPanels();

	var numTabbedPanels = Math.max(tabs.length, panels.length);

	for (var i = 0; i < numTabbedPanels; i++)
	{
		if (i != tpIndex)
		{
			if (tabs[i])
				this.removeClassName(tabs[i], this.tabSelectedClass);
			if (panels[i])
			{
				this.removeClassName(panels[i], this.panelVisibleClass);
				panels[i].style.display = "none";
			}
		}
	}

	this.addClassName(tabs[tpIndex], this.tabSelectedClass);
	this.addClassName(panels[tpIndex], this.panelVisibleClass);
	panels[tpIndex].style.display = "block";

	this.currentTabIndex = tpIndex;
};

Spry.Widget.TabbedPanels.prototype.attachBehaviors = function(element)
{
	var tabs = this.getTabs();
	var panels = this.getContentPanels();
	var panelCount = this.getTabbedPanelCount();

	for (var i = 0; i < panelCount; i++)
		this.addPanelEventListeners(tabs[i], panels[i]);

	this.showPanel(this.defaultTab);
};


// ****************** //
//     Accordion      //
// ****************** //


var Spry;
if (!Spry) Spry = {};
if (!Spry.Widget) Spry.Widget = {};

Spry.Widget.Accordion = function(element, opts)
{
	this.element = this.getElement(element);
	this.defaultPanel = 0;
	this.hoverClass = "AccordionPanelTabHover";
	this.openClass = "AccordionPanelOpen";
	this.closedClass = "AccordionPanelClosed";
	this.focusedClass = "AccordionFocused";
	this.enableAnimation = true;
	this.enableKeyboardNavigation = true;
	this.currentPanel = null;
	this.animator = null;
	this.hasFocus = null;

	this.previousPanelKeyCode = Spry.Widget.Accordion.KEY_UP;
	this.nextPanelKeyCode = Spry.Widget.Accordion.KEY_DOWN;

	this.useFixedPanelHeights = true;
	this.fixedPanelHeight = 0;

	Spry.Widget.Accordion.setOptions(this, opts, true);

	this.attachBehaviors();
};

Spry.Widget.Accordion.prototype.getElement = function(ele)
{
	if (ele && typeof ele == "string")
		return document.getElementById(ele);
	return ele;
};

Spry.Widget.Accordion.prototype.addClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) != -1))
		return;
	ele.className += (ele.className ? " " : "") + className;
};

Spry.Widget.Accordion.prototype.removeClassName = function(ele, className)
{
	if (!ele || !className || (ele.className && ele.className.search(new RegExp("\\b" + className + "\\b")) == -1))
		return;
	ele.className = ele.className.replace(new RegExp("\\s*\\b" + className + "\\b", "g"), "");
};

Spry.Widget.Accordion.setOptions = function(obj, optionsObj, ignoreUndefinedProps)
{
	if (!optionsObj)
		return;
	for (var optionName in optionsObj)
	{
		if (ignoreUndefinedProps && optionsObj[optionName] == undefined)
			continue;
		obj[optionName] = optionsObj[optionName];
	}
};

Spry.Widget.Accordion.prototype.onPanelTabMouseOver = function(e, panel)
{
	if (panel)
		this.addClassName(this.getPanelTab(panel), this.hoverClass);
	return false;
};

Spry.Widget.Accordion.prototype.onPanelTabMouseOut = function(e, panel)
{
	if (panel)
		this.removeClassName(this.getPanelTab(panel), this.hoverClass);
	return false;
};

Spry.Widget.Accordion.prototype.openPanel = function(elementOrIndex)
{
	var panelA = this.currentPanel;
	var panelB;

	if (typeof elementOrIndex == "number")
		panelB = this.getPanels()[elementOrIndex];
	else
		panelB = this.getElement(elementOrIndex);
	
	if (!panelB || panelA == panelB)	
		return null;

	var contentA = panelA ? this.getPanelContent(panelA) : null;
	var contentB = this.getPanelContent(panelB);

	if (!contentB)
		return null;

	if (this.useFixedPanelHeights && !this.fixedPanelHeight)
		this.fixedPanelHeight = (contentA.offsetHeight) ? contentA.offsetHeight : contentA.scrollHeight;

	if (this.enableAnimation)
	{
		if (this.animator)
			this.animator.stop();
		this.animator = new Spry.Widget.Accordion.PanelAnimator(this, panelB, { duration: this.duration, fps: this.fps, transition: this.transition });
		this.animator.start();
	}
	else
	{
		if(contentA)
		{
			contentA.style.display = "none";
			contentA.style.height = "0px";
		}
		contentB.style.display = "block";
		contentB.style.height = this.useFixedPanelHeights ? this.fixedPanelHeight + "px" : "auto";
	}

	if(panelA)
	{
		this.removeClassName(panelA, this.openClass);
		this.addClassName(panelA, this.closedClass);
	}

	this.removeClassName(panelB, this.closedClass);
	this.addClassName(panelB, this.openClass);

	this.currentPanel = panelB;

	return panelB;
};

Spry.Widget.Accordion.prototype.closePanel = function()
{
	// The accordion can only ever have one panel open at any
	// give time, so this method only closes the current panel.
	// If the accordion is in fixed panel heights mode, this
	// method does nothing.

	if (!this.useFixedPanelHeights && this.currentPanel)
	{
		var panel = this.currentPanel;
		var content = this.getPanelContent(panel);
		if (content)
		{
			if (this.enableAnimation)
			{
				if (this.animator)
					this.animator.stop();
				this.animator = new Spry.Widget.Accordion.PanelAnimator(this, null, { duration: this.duration, fps: this.fps, transition: this.transition });
				this.animator.start();
			}
			else
			{
				content.style.display = "none";
				content.style.height = "0px";
			}
		}		
		this.removeClassName(panel, this.openClass);
		this.addClassName(panel, this.closedClass);
		this.currentPanel = null;
	}
};

Spry.Widget.Accordion.prototype.openNextPanel = function()
{
	return this.openPanel(this.getCurrentPanelIndex() + 1);
};

Spry.Widget.Accordion.prototype.openPreviousPanel = function()
{
	return this.openPanel(this.getCurrentPanelIndex() - 1);
};

Spry.Widget.Accordion.prototype.openFirstPanel = function()
{
	return this.openPanel(0);
};

Spry.Widget.Accordion.prototype.openLastPanel = function()
{
	var panels = this.getPanels();
	return this.openPanel(panels[panels.length - 1]);
};

Spry.Widget.Accordion.prototype.onPanelTabClick = function(e, panel)
{
	if (panel != this.currentPanel)
		this.openPanel(panel);
	else
		this.closePanel();

	if (this.enableKeyboardNavigation)
		this.focus();

	if (e.preventDefault) e.preventDefault();
	else e.returnValue = false;
	if (e.stopPropagation) e.stopPropagation();
	else e.cancelBubble = true;

	return false;
};

Spry.Widget.Accordion.prototype.onFocus = function(e)
{
	this.hasFocus = true;
	this.addClassName(this.element, this.focusedClass);
	return false;
};

Spry.Widget.Accordion.prototype.onBlur = function(e)
{
	this.hasFocus = false;
	this.removeClassName(this.element, this.focusedClass);
	return false;
};

Spry.Widget.Accordion.KEY_UP = 38;
Spry.Widget.Accordion.KEY_DOWN = 40;

Spry.Widget.Accordion.prototype.onKeyDown = function(e)
{
	var key = e.keyCode;
	if (!this.hasFocus || (key != this.previousPanelKeyCode && key != this.nextPanelKeyCode))
		return true;
	
	var panels = this.getPanels();
	if (!panels || panels.length < 1)
		return false;
	var currentPanel = this.currentPanel ? this.currentPanel : panels[0];
	var nextPanel = (key == this.nextPanelKeyCode) ? currentPanel.nextSibling : currentPanel.previousSibling;

	while (nextPanel)
	{
		if (nextPanel.nodeType == 1 /* Node.ELEMENT_NODE */)
			break;
		nextPanel = (key == this.nextPanelKeyCode) ? nextPanel.nextSibling : nextPanel.previousSibling;
	}

	if (nextPanel && currentPanel != nextPanel)
		this.openPanel(nextPanel);

	if (e.preventDefault) e.preventDefault();
	else e.returnValue = false;
	if (e.stopPropagation) e.stopPropagation();
	else e.cancelBubble = true;

	return false;
};

Spry.Widget.Accordion.prototype.attachPanelHandlers = function(panel)
{
	if (!panel)
		return;

	var tab = this.getPanelTab(panel);

	if (tab)
	{
		var self = this;
		Spry.Widget.Accordion.addEventListener(tab, "click", function(e) { return self.onPanelTabClick(e, panel); }, false);
		Spry.Widget.Accordion.addEventListener(tab, "mouseover", function(e) { return self.onPanelTabMouseOver(e, panel); }, false);
		Spry.Widget.Accordion.addEventListener(tab, "mouseout", function(e) { return self.onPanelTabMouseOut(e, panel); }, false);
	}
};

Spry.Widget.Accordion.addEventListener = function(element, eventType, handler, capture)
{
	try
	{
		if (element.addEventListener)
			element.addEventListener(eventType, handler, capture);
		else if (element.attachEvent)
			element.attachEvent("on" + eventType, handler);
	}
	catch (e) {}
};

Spry.Widget.Accordion.prototype.initPanel = function(panel, isDefault)
{
	var content = this.getPanelContent(panel);
	if (isDefault)
	{
		this.currentPanel = panel;
		this.removeClassName(panel, this.closedClass);
		this.addClassName(panel, this.openClass);

		// Attempt to set up the height of the default panel. We don't want to
		// do any dynamic panel height calculations here because our accordion
		// or one of its parent containers may be display:none.

		if (content)
		{
			if (this.useFixedPanelHeights)
			{
				// We are in fixed panel height mode and the user passed in
				// a panel height for us to use.
	
				if (this.fixedPanelHeight)
					content.style.height = this.fixedPanelHeight + "px";
			}
			else
			{
				// We are in variable panel height mode, but since we can't
				// calculate the panel height here, we just set the height to
				// auto so that it expands to show all of its content.
	
				content.style.height = "auto";
			}
		}
	}
	else
	{
		this.removeClassName(panel, this.openClass);
		this.addClassName(panel, this.closedClass);

		if (content)
		{
			content.style.height = "0px";
			content.style.display = "none";
		}
	}
	
	this.attachPanelHandlers(panel);
};

Spry.Widget.Accordion.prototype.attachBehaviors = function()
{
	var panels = this.getPanels();
	for (var i = 0; i < panels.length; i++)
		this.initPanel(panels[i], i == this.defaultPanel);

	// Advanced keyboard navigation requires the tabindex attribute
	// on the top-level element.

	this.enableKeyboardNavigation = (this.enableKeyboardNavigation && this.element.attributes.getNamedItem("tabindex"));
	if (this.enableKeyboardNavigation)
	{
		var self = this;
		Spry.Widget.Accordion.addEventListener(this.element, "focus", function(e) { return self.onFocus(e); }, false);
		Spry.Widget.Accordion.addEventListener(this.element, "blur", function(e) { return self.onBlur(e); }, false);
		Spry.Widget.Accordion.addEventListener(this.element, "keydown", function(e) { return self.onKeyDown(e); }, false);
	}
};

Spry.Widget.Accordion.prototype.getPanels = function()
{
	return this.getElementChildren(this.element);
};

Spry.Widget.Accordion.prototype.getCurrentPanel = function()
{
	return this.currentPanel;
};

Spry.Widget.Accordion.prototype.getPanelIndex = function(panel)
{
	var panels = this.getPanels();
	for( var i = 0 ; i < panels.length; i++ )
	{
		if( panel == panels[i] )
			return i;
	}
	return -1;
};

Spry.Widget.Accordion.prototype.getCurrentPanelIndex = function()
{
	return this.getPanelIndex(this.currentPanel);
};

Spry.Widget.Accordion.prototype.getPanelTab = function(panel)
{
	if (!panel)
		return null;
	return this.getElementChildren(panel)[0];
};

Spry.Widget.Accordion.prototype.getPanelContent = function(panel)
{
	if (!panel)
		return null;
	return this.getElementChildren(panel)[1];
};

Spry.Widget.Accordion.prototype.getElementChildren = function(element)
{
	var children = [];
	var child = element.firstChild;
	while (child)
	{
		if (child.nodeType == 1 /* Node.ELEMENT_NODE */)
			children.push(child);
		child = child.nextSibling;
	}
	return children;
};

Spry.Widget.Accordion.prototype.focus = function()
{
	if (this.element && this.element.focus)
		this.element.focus();
};

Spry.Widget.Accordion.prototype.blur = function()
{
	if (this.element && this.element.blur)
		this.element.blur();
};

/////////////////////////////////////////////////////

Spry.Widget.Accordion.PanelAnimator = function(accordion, panel, opts)
{
	this.timer = null;
	this.interval = 0;

	this.fps = 0;
	this.duration = 0;
	this.startTime = 0;

	this.transition = Spry.Widget.Accordion.PanelAnimator.defaultTransition;

	this.onComplete = null;

	this.panel = panel;
	this.panelToOpen = accordion.getElement(panel);
	this.panelData = [];
	this.useFixedPanelHeights = accordion.useFixedPanelHeights;

	Spry.Widget.Accordion.setOptions(this, opts, true);

	this.interval = Math.floor(1000 / this.fps);

	// Set up the array of panels we want to animate.

	var panels = accordion.getPanels();
	for (var i = 0; i < panels.length; i++)
	{
		var p = panels[i];
		var c = accordion.getPanelContent(p);
		if (c)
		{
			var h = c.offsetHeight;
			if (h == undefined)
				h = 0;

			if (p == panel && h == 0)
				c.style.display = "block";

			if (p == panel || h > 0)
			{
				var obj = new Object;
				obj.panel = p;
				obj.content = c;
				obj.fromHeight = h;
				obj.toHeight = (p == panel) ? (accordion.useFixedPanelHeights ? accordion.fixedPanelHeight : c.scrollHeight) : 0;
				obj.distance = obj.toHeight - obj.fromHeight;
				obj.overflow = c.style.overflow;
				this.panelData.push(obj);

				c.style.overflow = "hidden";
				c.style.height = h + "px";
			}
		}
	}
};

Spry.Widget.Accordion.PanelAnimator.defaultTransition = function(time, begin, finish, duration) { time /= duration; return begin + ((2 - time) * time * finish); };

Spry.Widget.Accordion.PanelAnimator.prototype.start = function()
{
	var self = this;
	this.startTime = (new Date).getTime();
	this.timer = setTimeout(function() { self.stepAnimation(); }, this.interval);
};

Spry.Widget.Accordion.PanelAnimator.prototype.stop = function()
{
	if (this.timer)
	{
		clearTimeout(this.timer);

		// If we're killing the timer, restore the overflow
		// properties on the panels we were animating!

		for (i = 0; i < this.panelData.length; i++)
		{
			obj = this.panelData[i];
			obj.content.style.overflow = obj.overflow;
		}
	}

	this.timer = null;
};

Spry.Widget.Accordion.PanelAnimator.prototype.stepAnimation = function()
{
	var curTime = (new Date).getTime();
	var elapsedTime = curTime - this.startTime;

	var i, obj;

	if (elapsedTime >= this.duration)
	{
		for (i = 0; i < this.panelData.length; i++)
		{
			obj = this.panelData[i];
			if (obj.panel != this.panel)
			{
				obj.content.style.display = "none";
				obj.content.style.height = "0px";
			}
			obj.content.style.overflow = obj.overflow;
			obj.content.style.height = (this.useFixedPanelHeights || obj.toHeight == 0) ? obj.toHeight + "px" : "auto";
		}
		if (this.onComplete)
			this.onComplete();
		return;
	}

	for (i = 0; i < this.panelData.length; i++)
	{
		obj = this.panelData[i];
		var ht = this.transition(elapsedTime, obj.fromHeight, obj.distance, this.duration);
		obj.content.style.height = ((ht < 0) ? 0 : ht) + "px";
	}
	
	var self = this;
	this.timer = setTimeout(function() { self.stepAnimation(); }, this.interval);
};