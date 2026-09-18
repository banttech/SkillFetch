<?php

namespace App\Traits;

use App\Models\PostJob;
use App\Models\Supervisor;

trait SupervisorJobValidation
{
    /**
     * Get supervisor's skill and experience IDs
     */
    protected function getSupervisorSkillsAndExperiences(Supervisor $supervisor)
    {
        return [
            'skills' => $supervisor->skills()->pluck('skill_id')->toArray(),
            'experiences' => $supervisor->experiences()->pluck('experience_id')->toArray()
        ];
    }

    /**
     * Apply job matching filter to query
     */
    protected function applyJobMatchingFilter($query, array $supervisorSkillIds, array $supervisorExperienceIds)
    {
        if (!empty($supervisorSkillIds) || !empty($supervisorExperienceIds)) {
            $query->where(function ($q) use ($supervisorSkillIds, $supervisorExperienceIds) {
                if (!empty($supervisorSkillIds)) {
                    $q->whereIn('id', function ($subQuery) use ($supervisorSkillIds) {
                        $subQuery->select('post_job_id')
                            ->from('post_job_skills')
                            ->whereIn('skill_id', $supervisorSkillIds);
                    });
                }

                if (!empty($supervisorExperienceIds)) {
                    $q->orWhereIn('id', function ($subQuery) use ($supervisorExperienceIds) {
                        $subQuery->select('post_job_id')
                            ->from('post_job_experiences')
                            ->whereIn('experience_id', $supervisorExperienceIds);
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Validate if a job matches supervisor's skills or experiences
     * Returns job if valid, null if not
     */
    protected function validateJobForSupervisor($jobId, Supervisor $supervisor, $returnJob = false)
    {
        // Get the job
        $job = PostJob::with(['skills', 'experiences'])
            ->where('id', $jobId)
            ->where('status', 1)
            ->where('paymentStatus', 'paid')
            ->first();

        if (!$job) {
            return null;
        }

        // Get supervisor's skills and experiences
        $supervisorData = $this->getSupervisorSkillsAndExperiences($supervisor);
        
        // Get job's skill and experience IDs
        $jobSkillIds = $job->skills->pluck('id')->toArray();
        $jobExperienceIds = $job->experiences->pluck('id')->toArray();

        // Check for matches
        $hasMatchingSkills = !empty(array_intersect($supervisorData['skills'], $jobSkillIds));
        $hasMatchingExperiences = !empty(array_intersect($supervisorData['experiences'], $jobExperienceIds));

        // If no match, return null
        if (!$hasMatchingSkills && !$hasMatchingExperiences) {
            return null;
        }

        return $returnJob ? $job : true;
    }

    /**
     * Get matching and non-matching skills/experiences for a job
     */
    protected function getJobMatchDetails($job, array $supervisorSkillIds, array $supervisorExperienceIds)
    {
        return [
            'matchingSkills' => $job->skills->whereIn('id', $supervisorSkillIds),
            'nonMatchingSkills' => $job->skills->whereNotIn('id', $supervisorSkillIds),
            'matchingExperiences' => $job->experiences->whereIn('id', $supervisorExperienceIds),
            'nonMatchingExperiences' => $job->experiences->whereNotIn('id', $supervisorExperienceIds),
        ];
    }
}