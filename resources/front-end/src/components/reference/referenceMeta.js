import { markRaw } from 'vue';
import { Crosshair, Palette, Sun, MapPin, Store, Target } from 'lucide-vue-next';

/**
 * Shared metadata for every list managed through the "Manage Lists" surface and
 * the reusable Add/Edit modal. Keyed by reference type. This is the single source
 * of truth for titles, copy, icons, and grouping — mirrors the design's TYPE_META.
 *
 * @typedef {'caliber'|'purpose'|'location'|'store'|'range'} ReferenceType
 */
export const REFERENCE_TYPES = {
  caliber: {
    key: 'caliber',
    title: 'Calibers',
    singular: 'caliber',
    noun: 'calibers',
    addLabel: 'Add caliber',
    colName: 'Label · Official name',
    sub: 'Cartridge chamberings used across firearms and ammo.',
    kindSubline: 'Caliber · reference list',
    icon: markRaw(Crosshair),
    group: 'lists',
    linkable: false,
    field: {
      label: 'Label',
      labelSub: '· short name shown across Bang',
      placeholder: 'e.g. .308 Win',
      hint: 'The everyday name used on chips and dropdowns throughout the app.',
    },
  },
  purpose: {
    key: 'purpose',
    title: 'Purposes',
    singular: 'purpose',
    noun: 'purposes',
    addLabel: 'Add purpose',
    colName: 'Purpose',
    sub: 'What a load is for — shown as a tag on every ammo load.',
    kindSubline: 'Purpose · reference list',
    icon: markRaw(Sun),
    group: 'lists',
    linkable: false,
    field: {
      label: 'Purpose name',
      labelSub: '· shown as a tag on ammo loads',
      placeholder: 'e.g. Plinking',
      hint: 'Shown as a tag on ammo loads.',
    },
  },
  color: {
    key: 'color',
    title: 'Colors',
    singular: 'color',
    noun: 'colors',
    addLabel: 'Add color',
    colName: 'Color · Short label',
    sub: 'Finish and color choices used across firearms and accessories.',
    kindSubline: 'Color · reference list',
    icon: markRaw(Palette),
    group: 'lists',
    linkable: false,
    field: {
      label: 'Color name',
      labelSub: '· shown on firearm and accessory forms',
      placeholder: 'e.g. Flat Dark Earth',
      hint: 'Shown in optional color dropdowns throughout the app.',
    },
  },
  location: {
    key: 'location',
    title: 'Storage Locations',
    singular: 'location',
    noun: 'locations',
    addLabel: 'Add location',
    colName: 'Location',
    sub: 'Where firearms and gear are stored.',
    kindSubline: 'Storage location · reference list',
    icon: markRaw(MapPin),
    group: 'facilities',
    linkable: true,
    showRoute: { name: 'LocationsShow', param: 'location_id' },
    field: {
      label: 'Location name',
      labelSub: '',
      placeholder: 'e.g. Bedroom Safe',
      hint: "Shown wherever a firearm's storage location is listed.",
    },
  },
  store: {
    key: 'store',
    title: 'Stores',
    singular: 'store',
    noun: 'stores',
    addLabel: 'Add store',
    colName: 'Store',
    sub: 'Where you buy firearms, ammo, and gear — tracked on purchase records.',
    kindSubline: 'Store · reference list',
    icon: markRaw(Store),
    group: 'facilities',
    linkable: true,
    showRoute: { name: 'StoreShow', param: 'store_id' },
    field: {
      label: 'Store name',
      labelSub: '',
      placeholder: 'e.g. Bass Pro Shop',
      hint: 'Shown on purchase records for firearms, ammo, and gear.',
    },
  },
  range: {
    key: 'range',
    title: 'Ranges',
    singular: 'range',
    noun: 'ranges',
    addLabel: 'Add range',
    colName: 'Range',
    sub: 'Where you shoot — logged on every training session.',
    kindSubline: 'Range · reference list',
    icon: markRaw(Target),
    group: 'facilities',
    linkable: true,
    showRoute: { name: 'RangesShow', param: 'range_id' },
    field: {
      label: 'Range name',
      labelSub: '',
      placeholder: 'e.g. Eagle Point Range',
      hint: 'Shown on training sessions logged at this range.',
    },
  },
};

/** Ordered left-rail groups. */
export const REFERENCE_GROUPS = [
  { key: 'lists', label: 'Lists you manage', types: ['caliber', 'purpose', 'color'] },
  { key: 'facilities', label: 'Places & facilities', types: ['location', 'store', 'range'] },
];

function pluralize(count, noun) {
  return `${count} ${noun}${count === 1 ? '' : 's'}`;
}

/**
 * Total number of records referencing this item. Used both for the "Used by"
 * column and to guard deletion (an item in use can't be deleted).
 *
 * @param {ReferenceType} type
 * @param {object} item
 * @returns {number}
 */
export function usageOf(type, item) {
  if (!item) {
    return 0;
  }
  switch (type) {
    case 'caliber':
      return (item.firearms_count ?? 0) + (item.loads_count ?? 0);
    case 'purpose':
      return item.loads_count ?? 0;
    case 'color':
      return item.items_count ?? 0;
    case 'location':
      return locationUsage(item);
    case 'store':
      return item.orders_count ?? 0;
    case 'range':
      return item.sessions_count ?? 0;
    default:
      return 0;
  }
}

/**
 * Prefer the server's complete usage count, including sublocations and
 * relationships that aren't rendered as storage contents.
 */
function locationUsage(item) {
  if (item.usage_count !== undefined) {
    return item.usage_count;
  }

  const contents = item.contents ?? {};
  return (
    (item.children_count ?? 0) +
    ['firearms', 'suppressors', 'optics', 'lights', 'misc_accessories', 'magazines'].reduce(
      (sum, key) => sum + (contents[key]?.length ?? 0),
      0
    )
  );
}

const USAGE_UNITS = {
  purpose: 'load',
  color: 'item',
  location: 'item',
  store: 'order',
  range: 'session',
};

/**
 * Human-readable usage summary, e.g. "3 firearms · 6 loads" or "8 loads".
 *
 * @param {ReferenceType} type
 * @param {object} item
 * @returns {string}
 */
export function usageSummary(type, item) {
  if (type === 'caliber') {
    return `${pluralize(item?.firearms_count ?? 0, 'firearm')} · ${pluralize(
      item?.loads_count ?? 0,
      'load'
    )}`;
  }
  return pluralize(usageOf(type, item), USAGE_UNITS[type] ?? 'use');
}
