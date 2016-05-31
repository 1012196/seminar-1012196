{% extends '../../Backend/Views/layouts/layout-home.volt' %}

{% block content %}
    <!-- Panel Table Add Row -->
    <div class="page animsition">
        <div class="page-header">
            <ol class="breadcrumb">
                <li><a href="../index.html">Home</a></li>
                <li class="active">Basic UI</li>
            </ol>
            <h1 class="page-title">Management Comment</h1>
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

        <!-- Management Comment -->
        <div class="page-content container-fluid">
            <!-- Panel Tabs -->
            <div class="panel">
                <div class="row row-lg">
                    <div class="col-lg-12">
                        <div class="panel-heading">
                            <h3 class="panel-title">Not approved</h3>
                        </div>
                        <!-- Example Tabs -->
                        <div class="panel-body">
                            {{ flashSession.output() }}
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Article ID</th>
                                    <th hidden>Comment ID</th>
                                    <th>User avatar</th>
                                    <th>User ID</th>
                                    <th>User name</th>
                                    <th>User Full name</th>
                                    <th>Comment text</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                {% for row in cmt_not_approved %}
                                    <tr class="gradeA comment_row">
                                        <td class="article_id">{{ row['article_id'] }}</td>
                                        <td hidden
                                            class="comment_id">{{ row['comment_id'] }}</td>
                                        <td class="user_avatar"
                                            data-user_avatar="{{ row['user_avatar'] }}">
                                            <img src="https://graph.facebook.com/{{ row['user_avatar'] }}/picture?width=35&amp;height=35"
                                                 width="35">
                                        </td>
                                        <td class="user_id">{{ row['user_id'] }}</td>
                                        <td class="username">{{ row['username'] }}</td>
                                        <td class="user_full_name">{{ row['user_full_name'] }}</td>
                                        <td class="comment_text">{{ row['comment_text'] }}</td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-danger btn-xs ladda-button"
                                                    onclick="activateCmt($(this))"
                                                    data-style="expand-left"
                                                    data-plugin="ladda">
                                                <span class="ladda-label">Suspended</span>
                                                <span class="ladda-spinner"></span></button>
                                        </td>
                                        <td class="article_id">{{ (row['datetime_cmt'] / 1)|distanceOfTime }}</td>
                                        <td class="actions">
                                            <a href="#"
                                               class="btn btn-sm btn-icon btn-pure btn-default on-default remove-cmt"
                                               data-toggle="tooltip"
                                               data-original-title="Remove"><i
                                                        class="icon wb-trash"
                                                        aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                {% endfor %}
                                </tbody>
                            </table>
                            {% if  pages_cmt_not_approved is not 0 %}
                                <nav style="text-align: right">
                                    <ul class="pagination pagination-gap">
                                        <li class="disabled">
                                            <a href="javascript:void(0)" aria-label="Previous">
                                                <span aria-hidden="true">«</span>
                                            </a>
                                        </li>
                                        {% for page in 1..pages_cmt_not_approved %}
                                            <li>
                                                {{ link_to(url.get(['for': 'notApproved-comment-page', 'page': page]), page) }}
                                            </li>
                                        {% endfor %}
                                        <li>
                                            <a href="javascript:void(0)" aria-label="Next">
                                                <span aria-hidden="true">»</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            {% endif %}
                        </div>
                    </div>
                    <!-- End Management Reply -->
                </div>
                <div class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                        aria-label="Close">
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
                                <button type="button" class="btn btn-default margin-top-5"
                                        data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary margin-top-5">Save
                                    changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="real_user_id" value="{{ user_id|default(0) }}">
    <input type="hidden" id="real_user_name" value="{{ user_name|default('Anonymous') }}">
    <input type="hidden" id="real_user_full_name" value="{{ user_full_name|default('Anonymous') }}">
{% endblock %}
