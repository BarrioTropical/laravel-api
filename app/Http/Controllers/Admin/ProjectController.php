<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response 
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.projects.create', compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $slug = Project::generateSlug($request->title);
        $data['slug'] = $slug;
        if($request->hasFile('cover_image')){
            $path = Storage::put('project_images', $request->cover_image);
            $data['cover_image'] = $path; 
        }
        
        $new_project = Project::create($data);
        if($request->has('tags')){
            $new_project->tags()->attach($request->tags);
        }
        return redirect()->route('admin.projects.show', $new_project->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.projects.edit', compact('project', 'categories', 'tags'));
        
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request,Project $project)
    {
        $data = $request->validated();
        $slug = Project::generateSlug($request->title);
        $data['slug'] = $slug;
        if($request->hasFile('cover_image')){
            if ($project->cover_image){
                Storage::delete($project->cover_image);
            }
            $path = Storage::disk('public')->put('portfolio_images', $request->cover_image);
            $data['cover_image'] = $path; 
        }
        $project->update($data);
        if($request->has('tags')){
            $project->tags()->sync($request->tags);
        } else {
            $project->tags()->sync([]);
        }
        return redirect()->route('admin.project.index')->with('message', "$project->title deleted successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {   
        if($project->cover_image){
            Storage::delete($project->cover_image);
        }

        $project->delete();
        return redirect()->route('admin.project.index')->with('message', "$project->title deleted successfully");
    }
}
