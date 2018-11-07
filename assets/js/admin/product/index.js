import Grid from '../../../../../../admin-dev/themes/new-theme/js/components/grid/grid';
import FiltersResetExtension
  from "../../../../../../admin-dev/themes/new-theme/js/components/grid/extension/filters-reset-extension";
import SortingExtension
  from "../../../../../../admin-dev/themes/new-theme/js/components/grid/extension/sorting-extension";
import ReloadListExtension
  from "../../../../../../admin-dev/themes/new-theme/js/components/grid/extension/reload-list-extension";
import ExportToSqlManagerExtension
  from "../../../../../../admin-dev/themes/new-theme/js/components/grid/extension/export-to-sql-manager-extension";

const $ = window.$;

$(document).ready(() => {
  const productGrid = new Grid('masterclass_products');

  productGrid.addExtension(new FiltersResetExtension());
  productGrid.addExtension(new SortingExtension());
  productGrid.addExtension(new ReloadListExtension());
  productGrid.addExtension(new ExportToSqlManagerExtension());
});
