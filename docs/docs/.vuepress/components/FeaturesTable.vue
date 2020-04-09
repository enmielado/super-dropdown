<template>
  <div class="features-table">
    <div class="row one">
      <div class="feature">
        <h3>Create Dropdowns from Data</h3>
        <img src="img/features-basic.jpg" alt="Image">
        <p>Same as the native dropdown field, but populated with any dynamic data (Categories, Entries, static JSON, JSON from Twig).</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <h3>Create Cascading Sets of Dropdowns</h3>
        <img src="img/features-cascade.jpg" alt="Image">
        <p>Create a set of linked, hierarchical dropdowns simply by providing a Categories group (or Entry section), or any other data you supply from a Twig template.</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <h3>Create Adjacent Sets of Dropdowns</h3>
        <img src="img/features-adjacent.jpg" alt="Image">
        <p>Create a set of side-by-side dropdowns presented and saved as a single field.</p>
      </div>
    </div>
    <div class="row one">
      <div class="feature">
        <h3>Create Labeled Sets of Dropdowns</h3>
        <img src="img/features-sentence.jpg" alt="Image">
        <img src="img/features-condition.jpg" alt="Image">
        <p>Provide text labels that you can arrange inline or stacked to prepend and append to the dropdowns. You can create a multiple choice, complete-the-sentence style field, or a complex string of dropdowns that can include conditional dropdowns that show or hide depending on the value of another dropdowns.</p>
      </div>
    </div>
  </div>
</template>

<script>
    export default { }
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
    border-radius: 8px;
  }
  img + img {
    margin-top: 30px;
  }
</style>