import * as _ from 'lodash';

export default ({ store, Vue }) => {
  const permissions = {
    computed: {
      me() {
        return store.getters['users/getActiveUser'];
      },
      myPermissions() {
        return _.uniq(this.myRoles.flatMap((role) => role.permissions));
      },
      permissions() {
        return store.getters['roles/getPermissions'];
      },
    },
    methods: {
      can(action, model) {
        return this.myPermissions.includes(`${model}.${action}`);
      },
      cant(action, model) {
        return !this.can(action, model);
      },
    },
  };

  Vue.mixin(permissions);
};
