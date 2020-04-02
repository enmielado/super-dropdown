<template>
  <div class="features-table">
    <div class="row one">
      <div class="feature">
        <img src="img/features-basic.jpg" alt="Image">
        <h3>Dropdowns from Data</h3>
        <p>Same as the native dropdown field, but populated with any dynamic data (Categories, Entries, JSON).</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <img src="img/features-cascade.jpg" alt="Image">
        <h3>Cascading sets of Dropdowns</h3>
        <p>Create a set of linked, hierarchical dropdowns simply by providing a Categories group (or Entry section), or any other data you supply form a Twig template.</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <img src="img/features-adjacent.jpg" alt="Image">
        <h3>Adjacent Dropdowns</h3>
        <p>Create a set of side-by-side dropdowns presented and saved as a single field.</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <img src="img/features-sentence.jpg" alt="Image">
        <img src="img/features-condition.jpg" alt="Image">
        <h3>Labeled Sets of Dropdowns</h3>
        <p>Provide text labels that you can arrange inline or stacked to prepend and append to the dropdowns. You can create a multiple choice, complete-the-sentence style field, or a complex string of dropdowns that can include conditional dropdowns that show or hide depending on the value of another dropdowns.</p>
      </div>
    </div>
  </div>
</template>

<script>
    export default {

    }
</script>

<style lang="scss" scoped>
  .features-table {
    width: 100%;
  }
  .row {
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    width: 100%;
    &.one {
      > div {
        width: 100%;
      }
    }
    &.two {
      > div {
        width: 50%;
      }
    }
  }
  .feature {
    padding: 30px 0;
  }
  
  img {
    box-shadow: 5px 5px 35px rgba(33,33,33,0.2);
    border-radius: 10px;
  }
  img + img {
    margin-top: 30px;
  }
</style>