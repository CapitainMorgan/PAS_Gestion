<template>
  <div class="container">
    <h1>Statistiques</h1>
    <div class="row">
      <div class="col-md-3">
        <div class="stat-box">
          <h2>Quotidienne</h2>
          <p>{{ dailyStats }}</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <h2>Hebdomadaire</h2>
          <p>{{ weeklyStats }}</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <h2>Mensuelle</h2>
          <p>{{ monthlyStats }}</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <h2>Annuelle</h2>
          <p>{{ yearlyStats }}</p>
        </div>
      </div>
    </div>
    <button @click="exportData">Exporter Excel (TVA, impôt...)</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dailyStats: 0,
      weeklyStats: 0,
      monthlyStats: 0,
      yearlyStats: 0
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    fetchStats() {
      // Vous pouvez utiliser Axios ou Fetch pour récupérer les statistiques via l'API Laravel.
      // Voici un exemple de données statiques pour les tests.
      this.dailyStats = 120;
      this.weeklyStats = 560;
      this.monthlyStats = 2400;
      this.yearlyStats = 30000;
    },
    exportData() {
      // Logique pour exporter les données Excel
      alert('Exportation des données en Excel');
    }
  }
};
</script>

<style scoped>
.stat-box {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  margin-bottom: 20px;
}
</style>