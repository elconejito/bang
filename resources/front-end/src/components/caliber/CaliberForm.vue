<template>
  <form>
    <div class="form-group row">
      <label for="label" class="col-sm-2 col-form-label">Label</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="label" name="label" placeholder="Label of Caliber" required>
        <small class="form-text text-muted">
          The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
        </small>
      </div>
    </div>

    <div class="form-group row">
      <label for="label" class="col-sm-2 col-form-label">Short Label</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="short_label" name="short_label" placeholder="Short Label for Caliber" required>
        <small class="form-text text-muted">
          The label used throughout the app
        </small>
      </div>
    </div>

    <div class="form-group row">
      <label for="caliber_type_id" class="col-sm-2 col-form-label">Caliber Type</label>
      <div class="col-sm-10">
        <select class="form-control" id="caliber_type_id" name="caliber_type_id" required>
          <option v-for="(caliberType, i) in caliberTypes" :value="caliberType.id" :key="i">
            {{ caliberType.label }}
          </option>
        </select>
        <small class="form-text text-muted">
          The type of caliber such as rimfire, centerfire, or shotgun
        </small>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-10 offset-sm-2">
        <button type="submit" class="btn btn-primary">Add New</button>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  name: 'CaliberForm',
  data() {
    return {
      caliber: {},
    };
  },
  computed: {
    caliberTypes() {
      return [];
    },
  },
};
</script>

<style scoped></style>
