<?php
namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Backend\RoleCreateRequest;
use App\Http\Requests\Backend\RoleUpdateRequest;
use App\Models\Role;
use App\Services\RoleService;
use App\Transformers\Backend\PermissionTransformer;
use App\Transformers\Backend\RoleTransformer;


class RolesController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 显示指定角色
     * @param Role $role
     * @return \App\Support\TransformerResponse
     */
    public function show(Role $role)
    {
        return $this->response()->item($role, new RoleTransformer());
    }

    /**
     * 获取所有角色(不分页 用于添加用户时显示)
     * @return \App\Support\TransformerResponse
     */
    public function allRoles()
    {
        $roles = Role::ordered()->recent()->get();
        return $this->response()->collection($roles, new RoleTransformer());
    }

    /**
     * 角色列表
     * @return \App\Support\TransformerResponse
     */
    public function index()
    {
        $roles = Role::withSimpleSearch()
            ->withSort()
            ->ordered()
            ->recent()
            ->paginate($this->perPage());
        return $this->response()->paginator($roles, new RoleTransformer())
            ->setMeta(Role::getAllowSearchFieldsMeta() + Role::getAllowSortFieldsMeta());
    }

    /**
     * 获取指定角色下面的权限
     * @param Role $role
     * @return \App\Support\TransformerResponse
     */
    public function permissions(Role $role)
    {
        $permissions = $role->permissions()->ordered()->recent()->get();
        return $this->response()->collection($permissions, new PermissionTransformer());
    }


    /**
     * 创建角色
     * @param RoleCreateRequest $request
     * @return mixed
     */
    public function store(RoleCreateRequest $request, RoleService $roleService)
    {
        $roleService->create($request->validated());
        Role::create($request->all());
        return $this->response()->noContent();
    }

    /**
     * 更新角色
     * @param Role $role
     * @param RoleUpdateRequest $request
     * @return mixed
     */
    public function update(Role $role, RoleUpdateRequest $request, RoleService $roleService)
    {
        $roleService->update($role, $request->validated());
        return $this->response()->noContent();
    }

    /**
     * 删除角色
     * @param Role $role
     * @return mixed
     */
    public function destroy(Role $role)
    {
        // todo 删除关联数据
        $role->delete();
        return $this->response()->noContent();
    }
}
