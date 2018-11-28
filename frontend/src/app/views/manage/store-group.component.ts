import {Component, OnInit, ViewChild} from '@angular/core';
import {MatDialog, MatDialogConfig, MatPaginator, MatSort, MatTableDataSource} from '@angular/material';
import {StoresService} from '../../services/stores.service';
import {StoreGroupService} from '../../services/store-group.service';
import {environment} from '../../../environments/environment';
import {Store} from '../../models/store.model';
import {StoreGroup} from '../../models/store-group.model';
import {StoreComponent} from './store/store.component';
import swal from 'sweetalert2';
import {StoreGroupFormComponent} from './store-group-form/store-group-form.component';

@Component({
  selector: 'app-store-group',
  templateUrl: './store-group.component.html',
})
export class StoreGroupComponent implements OnInit {

  searchKey: string;
  stores_image_path: string = environment.stores_image_path;
  store_list: MatTableDataSource<any>;
  matDialogConfig: MatDialogConfig = new MatDialogConfig();
  selected_store: StoreGroup;
  stores_to_delete: StoreGroup[] = [];
  c_all = false;
  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  displayedColumns: string[] = [
    'name',
    'tel_number',
    'store_representatives_name',
    'store_representatives_email',
    'avatar',
    'address',
    'actions',
    'delete_actions'

  ];

  constructor(private storesGroupService: StoreGroupService,  private dialog: MatDialog) {
    this.matDialogConfig.disableClose = true;
    this.matDialogConfig.autoFocus = true;
    this.matDialogConfig.width = '50%';
  }

  ngOnInit() {
    this.refresh();
  }
  refresh() {
    this.storesGroupService.list_store_groups().subscribe(
      list => {
        const array = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        this.store_list = new MatTableDataSource(array);
        this.store_list.sort = this.sort;
        this.store_list.paginator = this.paginator;
      }
    );
  }
  onSearchClear() {
    this.searchKey = '';
    this.applyFilter();
  }
  applyFilter() {
    this.store_list.filter = this.searchKey.trim().toLowerCase();
  }

  onCreate() {
    this.dialog.open(StoreGroupFormComponent, this.matDialogConfig)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
  }

  onEdit(item: StoreGroup) {
    this.storesGroupService.setStoreGroup(item);
    this.dialog.open(StoreGroupFormComponent, this.matDialogConfig)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
  }

  checkAll(c: boolean) {
    this.c_all = c;
    this.store_list.data.map((store_group: StoreGroup) => {
      this.push( this.c_all, store_group);
    });
  }

  push(c: boolean, store_group: StoreGroup) {
    if ( c && this.stores_to_delete.indexOf(store_group) === -1) {
      this.stores_to_delete.push(store_group);
    }
    if (!c && this.stores_to_delete.indexOf(store_group) !== -1) {
      this.stores_to_delete.splice( this.stores_to_delete.indexOf(store_group), 1);
    }
  }

  onDelete(item: Store) {
    swal({
      title: 'Are you sure that you want to delete thus record ?',
      text: 'You won\'t be able to revert this!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.value) {
        this.storesGroupService.delete(item.id)
          .then(res => {
            swal(
              'Deleted!',
              'Record deleted.',
              'success'
            );
            this.refresh();
          }, reject => {
            swal(
              'Error!',
              'Error While trying to delete',
              'error'
            );
          });

      }
    });
  }

  onBulkDelete() {
    swal({
      title: 'Are you sure that you want to delete the selected record(s)?',
      text: 'You won\'t be able to revert this!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete!'
    }).then((result) => {
      if (result.value) {
        this.storesGroupService.bulk_delete(this.stores_to_delete)
          .then(res => {
            swal(
              'Deleted!',
              'Record deleted.',
              'success'
            );
            this.refresh();
          }, reject => {
            swal(
              'Error!',
              'Error While trying to delete',
              'error'
            );
          });

      }
    });
  }

}
