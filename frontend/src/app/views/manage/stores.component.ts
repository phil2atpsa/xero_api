import {Component, OnInit, ViewChild} from '@angular/core';
import {StoresService} from '../../services/stores.service';
import {MatSort, MatTableDataSource, MatPaginator, MatDialog, MatDialogConfig} from '@angular/material';

import {StoreComponent} from './store/store.component';
import {Store} from '../../models/store.model';
import swal from 'sweetalert2';
import {environment} from '../../../environments/environment';



@Component({
  selector: 'app-stores',
  templateUrl: './stores.component.html',

})
export class StoresComponent implements OnInit {

  searchKey: string;
  stores_image_path: string = environment.stores_image_path;
  store_list: MatTableDataSource<any>;
  matDialogConfig: MatDialogConfig = new MatDialogConfig();
  selected_store: Store;
  stores_to_delete: Store[] = [];
  c_all = false;


  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  displayedColumns: string[] = [
    'name',
    'parent_id',
    'shopping_center',
    'address',
    'tel_number',
    'avatar',
    'actions',
    'delete_actions'

  ];
  constructor(private storesService: StoresService,  private dialog: MatDialog) {
    this.matDialogConfig.disableClose = true;
    this.matDialogConfig.autoFocus = true;
    this.matDialogConfig.width = '75%';
  }

  ngOnInit() {
    /*this.stores_to_delete = [];*/
    this.refresh();
  }

  refresh() {
    this.storesService.getStores().subscribe(
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
    this.dialog.open(StoreComponent, this.matDialogConfig)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
  }

  onEdit(item: Store) {
    this.storesService.setStore(item);
    this.dialog.open(StoreComponent, this.matDialogConfig)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
  }

  showAdditionalInfos(event, item: Store) {
    event.preventDefault();
    this.storesService.setStore(item);
    this.selected_store = this.storesService.getStore();
  }
  closeAdditionalInfosWindow() {
    this.storesService.setStore(null);
    this.selected_store = this.storesService.getStore();
  }
  checkAll(c: boolean) {
    this.c_all = c;
    this.store_list.data.map((store: Store) => {
      this.push( this.c_all, store);
    });
  }

  push(c: boolean, store: Store) {
    if ( c && this.stores_to_delete.indexOf(store) === -1) {
      this.stores_to_delete.push(store);
    }
    if (!c && this.stores_to_delete.indexOf(store) !== -1) {
      this.stores_to_delete.splice( this.stores_to_delete.indexOf(store), 1);
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
        this.storesService.delete(item.id)
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
        this.storesService.bulk_delete(this.stores_to_delete)
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
