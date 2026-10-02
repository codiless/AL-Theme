(function (wp) {
  const { createElement: h, Fragment, useEffect } = wp.element;
  const { __ } = wp.i18n;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, SelectControl, TextControl, RangeControl, ToggleControl, Button } = wp.components;
  const { useSelect } = wp.data;
  const SSR = wp.serverSideRender.default || wp.serverSideRender;
  const ids = (s) => s.split(',').map(Number).filter((n) => Number.isInteger(n) && n > 0);
  const metadata = window.alBaseEditor || { fields: [], variants: { default: 'Default' } };
  wp.blocks.registerBlockType('al-base/content-listing', {
    edit({ attributes: a, setAttributes: set, clientId }) {
      const data = useSelect((select) => ({
        types: select('core').getPostTypes({ per_page: -1 }),
        taxonomies: select('core').getTaxonomies({ per_page: -1 }),
        postId: select('core/editor')?.getCurrentPostId(),
        blocks: select('core/block-editor').getBlocks(),
      }), []);
      useEffect(() => {
        const flatten = (blocks) => blocks.flatMap((block) => [block, ...flatten(block.innerBlocks || [])]);
        const duplicates = flatten(data.blocks).filter((b) => b.name === 'al-base/content-listing' && b.attributes.listingId === a.listingId);
        if (!a.listingId || (duplicates.length > 1 && duplicates[0].clientId !== clientId)) set({ listingId: `listing-${clientId.slice(0, 8)}` });
      }, [a.listingId, data.blocks, clientId]);
      const select = (label, key, options) => h(SelectControl, { label, value: a[key], options, onChange: (value) => set({ [key]: value }) });
      const number = (label, key, min, max) => h(RangeControl, { label, value: a[key], min, max, onChange: (value) => set({ [key]: value }) });
      const idInput = (label, key) => h(TextControl, { label, value: (a[key] || []).join(','), help: __('Comma-separated IDs. Manual order is preserved.', 'al-base'), onChange: (value) => set({ [key]: ids(value) }) });
      const text = (label, key) => h(TextControl, { label, value: a[key], onChange: (value) => set({ [key]: value }) });
      const fieldOptions = (predicate = () => true) => [{ label: __('None', 'al-base'), value: '' }, ...metadata.fields.filter(predicate)];
      const controls = h(InspectorControls, null,
        h(PanelBody, { title: __('Source and query', 'al-base') },
          select(__('Source', 'al-base'), 'source', [
            { value: 'posts', label: __('Posts / pages / CPT', 'al-base') },
            { value: 'terms', label: __('Taxonomy terms', 'al-base') },
            { value: 'children', label: __('Children', 'al-base') },
            { value: 'siblings', label: __('Siblings', 'al-base') },
            { value: 'related', label: __('Related by taxonomy', 'al-base') },
            { value: 'relationship', label: __('Related by public field', 'al-base') },
            { value: 'manual', label: __('Manual selection', 'al-base') },
          ]),
          a.source !== 'terms' && select(__('Post type', 'al-base'), 'postType', (data.types || []).filter((t) => t.viewable && t.slug !== 'attachment').map((t) => ({ label: t.name, value: t.slug }))),
          select(__('Taxonomy', 'al-base'), 'taxonomy', (data.taxonomies || []).filter((t) => t.visibility?.public && (a.source === 'terms' || t.types?.includes(a.postType))).map((t) => ({ label: t.name, value: t.slug }))),
          idInput(__('Term IDs', 'al-base'), 'terms'),
          a.source === 'terms' && h(ToggleControl, { label: __('Hide empty terms', 'al-base'), checked: a.hideEmpty, onChange: (hideEmpty) => set({ hideEmpty }) }),
          a.source === 'children' && h(TextControl, { label: __('Parent ID (0 = current page)', 'al-base'), type: 'number', value: a.parent, onChange: (v) => set({ parent: Math.max(0, Number(v) || 0) }) }),
          a.source === 'relationship' && select(__('Relationship field', 'al-base'), 'relationField', fieldOptions((f) => f.relationship)),
          a.source !== 'terms' && idInput(__('Include / manual IDs', 'al-base'), 'include'),
          a.source !== 'terms' && idInput(__('Exclude IDs', 'al-base'), 'exclude'),
          a.source !== 'terms' && h(TextControl, { label: __('Author ID (0 = any)', 'al-base'), type: 'number', value: a.author, onChange: (v) => set({ author: Math.max(0, Number(v) || 0) }) }),
          select(__('Order by', 'al-base'), 'orderBy', (a.source === 'terms' ? [['name', __('Name', 'al-base')], ['count', __('Count', 'al-base')], ['slug', __('Slug', 'al-base')], ['include', __('Manual order', 'al-base')]] : [['date', __('Date', 'al-base')], ['title', __('Title', 'al-base')], ['menu_order', __('Menu order', 'al-base')], ['rand', __('Random (avoid with pagination)', 'al-base')]]).map(([value, label]) => ({ value, label }))),
          select(__('Direction', 'al-base'), 'order', [{ label: __('Descending', 'al-base'), value: 'DESC' }, { label: __('Ascending', 'al-base'), value: 'ASC' }]),
          a.source !== 'terms' && select(__('Sort by public field', 'al-base'), 'sortField', fieldOptions((f) => f.sortable)),
          number(__('Items per page', 'al-base'), 'perPage', 1, 100), number(__('Offset', 'al-base'), 'offset', 0, 100),
          select(__('Pagination', 'al-base'), 'pagination', [{ label: __('None', 'al-base'), value: 'none' }, { label: __('Page links', 'al-base'), value: 'pagination' }, { label: __('Load more', 'al-base'), value: 'load-more' }]),
        ),
        h(PanelBody, { title: __('Layout', 'al-base'), initialOpen: false },
          select(__('Layout', 'al-base'), 'layout', [['grid', __('Grid', 'al-base')], ['list', __('List', 'al-base')], ['horizontal', __('Horizontal', 'al-base')], ['featured', __('Featured + grid', 'al-base')], ['compact', __('Compact', 'al-base')]].map(([value, label]) => ({ value, label }))),
          number(__('Desktop columns', 'al-base'), 'columns', 1, 6), number(__('Tablet columns', 'al-base'), 'tabletColumns', 1, 4), number(__('Mobile columns', 'al-base'), 'mobileColumns', 1, 2),
          select(__('Gap', 'al-base'), 'gap', ['xs', 's', 'm', 'l', 'xl', '2xl'].map((value) => ({ value, label: value.toUpperCase() }))),
        ),
        h(PanelBody, { title: __('Card and dynamic data', 'al-base'), initialOpen: false },
          select(__('Card variant', 'al-base'), 'cardVariant', Object.entries(metadata.variants).map(([value, label]) => ({ value, label }))),
          number(__('Heading level', 'al-base'), 'headingLevel', 2, 6),
          ...[['image', __('Image', 'al-base')], ['category', __('Category', 'al-base')], ['title', __('Title', 'al-base')], ['excerpt', __('Excerpt', 'al-base')], ['author', __('Author', 'al-base')], ['date', __('Date', 'al-base')], ['readingTime', __('Reading time', 'al-base')], ['fields', __('Custom fields', 'al-base')], ['badge', __('Badge', 'al-base')], ['button', __('Read more link', 'al-base')]].map(([key, label]) => h(ToggleControl, { key, label, checked: a.elements.includes(key), onChange: (enabled) => set({ elements: enabled ? [...a.elements, key] : a.elements.filter((e) => e !== key) }) })),
          h('p', null, __('Element order', 'al-base')),
          ...a.elements.map((element, index) => h('div', { key: element }, h('code', null, element), h(Button, { disabled: index === 0, 'aria-label': __('Move up', 'al-base'), onClick: () => { const elements = [...a.elements]; [elements[index - 1], elements[index]] = [elements[index], elements[index - 1]]; set({ elements }); } }, '↑'))),
          ...metadata.fields.filter((f) => !f.relationship).map((f) => h(ToggleControl, { key: f.value, label: f.label, checked: a.fields.includes(f.value), onChange: (on) => set({ fields: on ? [...a.fields, f.value] : a.fields.filter((v) => v !== f.value) }) })),
          select(__('Badge field', 'al-base'), 'badgeField', fieldOptions((f) => !f.relationship)),
          text(__('Stable listing ID', 'al-base'), 'listingId'),
        ),
      );
      return h(Fragment, null, controls, h('div', useBlockProps(), h(SSR, { block: 'al-base/content-listing', attributes: a, urlQueryArgs: { post_id: data.postId || 0 } })));
    },
    save: () => null,
  });
  wp.blocks.registerBlockType('al-base/breadcrumbs', {
    title: __('Breadcrumbs', 'al-base'), icon: 'arrow-right-alt2', category: 'theme',
    edit: () => h('div', useBlockProps(), h('p', null, __('Breadcrumbs appear on the published page.', 'al-base'))), save: () => null,
  });
  wp.blocks.registerBlockVariation('core/query', {
    name: 'al-base/latest', title: __('AL Latest content', 'al-base'),
    description: __('Native Query Loop with responsive cards.', 'al-base'),
    attributes: { namespace: 'al-base/latest', query: { perPage: 6, pages: 0, offset: 0, postType: 'post', order: 'desc', orderBy: 'date', inherit: false }, className: 'al-native-query' },
    isActive: ['namespace'], scope: ['inserter'],
    innerBlocks: [['core/post-template', { layout: { type: 'grid', columnCount: 3 } }, [['core/post-featured-image', { isLink: true, aspectRatio: '3/2' }], ['core/post-title', { isLink: true, level: 2 }], ['core/post-excerpt'], ['core/post-date']]], ['core/query-pagination', {}, [['core/query-pagination-previous'], ['core/query-pagination-numbers'], ['core/query-pagination-next']]], ['core/query-no-results', {}, [['core/paragraph', { content: __('No content found.', 'al-base') }]]]],
  });
})(window.wp);
