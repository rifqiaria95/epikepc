<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\PurchaseOrder;
use App\Models\Program;
use App\Models\JenisProgram;
use App\Models\ProgramRegistration;


function totalKaryawan()
{
    return Karyawan::count();
}

function totalAbsensi()
{
    return Absensi::count();

}

function totalPurchase()
{
    return PurchaseOrder::count();
}

function poPending()
{
    return PurchaseOrder::where('status', '=', 'Pending')->count();
}

function poSelesai()
{
    return PurchaseOrder::where('status', '=', 'Selesai')->count();
}

function totalUser()
{
    return User::count();

}
function totalPegawai()
{
    return Pegawai::count();
}

function totalActiveUser()
{
    return User::where('status_user', '>', 0)->count();

}

function totalInactiveUser()
{
    return User::where('status_user', '<', 1)->count();
}

function totalProgram()
{
    return Program::count();
}

function openProgram()
{
    return Program::where('status', '=', 'open')->count();
}

function totalDraftProgram()
{
    return Program::where('status', '=', 'draft')->count();
}

function totalClosedProgram()
{
    return Program::where('status', '=', 'closed')->count();
}

function totalJenisProgram()
{
    return JenisProgram::count();
}

function totalProgramRegistration()
{
    return ProgramRegistration::count();
}

function totalPendaftar()
{
    return ProgramRegistration::with('user')->count();
}

function totalPendaftarPending()
{
    return ProgramRegistration::where('status', '=', 'pending')->count();
}

function totalPendaftarApproved()
{
    return ProgramRegistration::where('status', '=', 'approved')->count();
}

function totalPendaftarRejected()
{
    return ProgramRegistration::where('status', '=', 'rejected')->count();
}
