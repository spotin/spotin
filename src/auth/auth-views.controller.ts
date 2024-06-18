import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Controller, Get, Query, Render, Res } from '@nestjs/common';
import { User } from '@prisma/client';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { Response } from 'express';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
	@Get('login')
	@ApiOperation({
		summary: 'Render the login page',
		description: 'Render the login page.',
		operationId: 'renderLogin',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/login')
	renderLogin() {
		return {
			title: 'Log in | Spot in',
		};
	}

	@Get('logout')
	@ApiOperation({
		summary: 'Render the logout page',
		description: 'Render the logout page.',
		operationId: 'renderLogout',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/logout')
	renderLogout(@Res({ passthrough: true }) res: Response) {
		res.clearCookie('jwt');

		return {
			title: 'Logout | Spot in',
		};
	}

	@Get('register')
	@ApiOperation({
		summary: 'Render the signup page',
		description: 'Render the signup page.',
		operationId: 'renderRegister',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/register')
	renderRegister() {
		return {
			title: 'Register | Spot in',
		};
	}

	@Get('reset-password-request')
	@ApiOperation({
		summary: 'Render the reset password request page',
		description: 'Render the reset password request page.',
		operationId: 'renderResetPasswordRequest',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/reset-password-request')
	renderResetPasswordRequest() {
		return {
			title: 'Reset password request | Spot in',
		};
	}

	@Get('reset-password')
	@ApiOperation({
		summary: 'Render the reset password page',
		description: 'Render the reset password page.',
		operationId: 'renderResetPassword',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderResetPassword(@Res() res: Response, @Query('token') token: string) {
		if (!token) {
			return res.redirect('/auth/reset-password-request');
		}

		return res.render('auth/reset-password', {
			title: 'Reset password | Spot in',
			token,
		});
	}

	@Get('profile')
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the profile page',
		description: 'Render the profile page.',
		operationId: 'renderProfile',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/profile')
	renderProfile(@AuthUser() user: User) {
		return {
			title: 'Profile | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}
}
