{% extends '../../Backend/Views/layouts/layout-home.volt' %}

{% block content %}
    <!-- Panel Table Add Row -->
    <div class="page animsition">
        <div class="page-header">
            <ol class="breadcrumb">
                <li><a href="../index.html">Home</a></li>
                <li class="active">Basic UI</li>
            </ol>
            <h1 class="page-title">Management Like</h1>
            <div class="page-header-actions">
                <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round" data-toggle="tooltip"
                        data-original-title="Edit">
                    <i class="icon wb-pencil" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round" data-toggle="tooltip"
                        data-original-title="Refresh">
                    <i class="icon wb-refresh" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-inverse btn-round" data-toggle="tooltip"
                        data-original-title="Setting">
                    <i class="icon wb-settings" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Management Like -->
        <div class="page-content container-fluid">
            <!-- Panel Tabs -->
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Like</h3>
                </div>
                <div class="panel-body container-fluid">
                    <div class="row row-lg">
                        <div class="col-lg-12">
                            <!-- Example Tabs -->
                            <table class="table table-bordered table-hover table-striped" id="exampleAddRow">
                                <thead>
                                <tr role="row">
                                    <th>Owner ID</th>
                                    <th hidden>Comment ID</th>
                                    <th>Article ID</th>
                                    <th>User ID</th>
                                    <th>User name</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="gradeA">
                                    <td class="owner_id">dd</td>
                                    <td class="comment_id" hidden>ddd</td>
                                    <td class="article_id">sd</td>
                                    <td class="user_id">sd</td>
                                    <td class="username">da</td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- End Example Tabs -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Management Like -->
    </div>

    <div class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Modal Title</h4>
                </div>
                <div class="modal-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Integer nec odio. Praesent libero. Sed cursus ante
                        dapibus diam. Sed nisi. Nulla quis sem at nibh elementum
                        imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce
                        nec tellus sed augue semper porta. Mauris massa. Vestibulum
                        lacinia arcu eget nulla.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default margin-top-5" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary margin-top-5">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="real_user_id" value="{{ user_id|default(0) }}">
    <input type="hidden" id="real_user_name" value="{{ user_name|default('Anonymous') }}">
{% endblock %}
