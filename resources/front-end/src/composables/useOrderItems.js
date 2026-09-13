export const ORDER_ASSET_TYPES = [
  'firearm',
  'suppressor',
  'optic',
  'light',
  'misc-accessory',
  'mount',
  'magazine',
];

export function orderItemLabel(item) {
  const resource = item?.ammunition ?? item?.asset;
  return (
    [resource?.manufacturer, resource?.label].filter(Boolean).join(' · ') || item?.label || 'Item'
  );
}

export function orderItemSearchText(item) {
  const resource = item?.ammunition ?? item?.asset;
  return [
    item?.type,
    item?.label,
    item?.manufacturer,
    resource?.manufacturer,
    resource?.label,
    resource?.secondary_label,
    resource?.caliber?.label,
    resource?.weight,
    item?.secondary_label,
  ]
    .filter(Boolean)
    .join(' ')
    .toLowerCase();
}

export function isAmmoItem(item) {
  return item?.type === 'ammunition' || !!item?.ammunition_id;
}
export function itemRounds(items) {
  return items.reduce((sum, item) => sum + (isAmmoItem(item) ? Number(item.rounds) || 0 : 0), 0);
}
export function itemCost(items) {
  return items.reduce((sum, item) => sum + (Number(item.cost) || 0), 0);
}

export function normalizeOrderItem(item = {}) {
  const ammo = isAmmoItem(item);
  return {
    id: item.id,
    type: item.type ?? (ammo ? 'ammunition' : 'asset'),
    ammunition_id: item.ammunition_id ?? item.ammunition?.id ?? '',
    asset_id: item.asset_id ?? item.asset?.id ?? '',
    rounds: item.rounds ?? '',
    cost: item.cost ?? '',
    _key: item._key ?? `order-item-${Math.random().toString(36).slice(2)}`,
  };
}
