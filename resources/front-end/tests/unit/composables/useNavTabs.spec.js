import { describe, it, expect } from 'vitest';
import { useNavTabs } from '@/composables/useNavTabs';

const MockA = { template: '<div>A</div>' };
const MockB = { template: '<div>B</div>' };
const MockC = { template: '<div>C</div>' };

function makeTabMapping() {
  return {
    details: { active: true, label: 'Details', component: MockA },
    inventory: { active: false, label: 'Inventory', component: MockB },
    images: { active: false, label: 'Images', component: MockC },
  };
}

describe('useNavTabs', () => {
  it('starts with no tabs', () => {
    const { currentTab } = useNavTabs();
    expect(currentTab.value).toBeUndefined();
  });

  it('initTabs sets the active tab', () => {
    const { initTabs, currentTab } = useNavTabs();
    initTabs(makeTabMapping());
    expect(currentTab.value).toBe('details');
  });

  it('tabNames returns labels in definition order', () => {
    const { initTabs, tabNames } = useNavTabs();
    initTabs(makeTabMapping());
    expect(tabNames.value).toEqual(['Details', 'Inventory', 'Images']);
  });

  it('currentTabComponent returns the active component object', () => {
    const { initTabs, currentTabComponent } = useNavTabs();
    initTabs(makeTabMapping());
    expect(currentTabComponent.value).toStrictEqual(MockA);
  });

  it('setCurrentTab switches the active tab', () => {
    const { initTabs, currentTab, currentTabComponent, setCurrentTab } = useNavTabs();
    initTabs(makeTabMapping());
    setCurrentTab('inventory');
    expect(currentTab.value).toBe('inventory');
    expect(currentTabComponent.value).toStrictEqual(MockB);
  });

  it('setCurrentTab deactivates the previous tab', () => {
    const { initTabs, tabs, setCurrentTab } = useNavTabs();
    initTabs(makeTabMapping());
    setCurrentTab('images');
    expect(tabs.value.details.active).toBe(false);
    expect(tabs.value.images.active).toBe(true);
  });

  it('tabNameSlug converts label to camelCase key', () => {
    const { tabNameSlug } = useNavTabs();
    expect(tabNameSlug('Details')).toBe('details');
    expect(tabNameSlug('My Inventory')).toBe('myInventory');
  });
});
