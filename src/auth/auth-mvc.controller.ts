import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Response } from 'express';
import {
	Body,
	Controller,
	Get,
	HttpCode,
	Post,
	Render,
	Res,
	Session,
	UseFilters,
} from '@nestjs/common';
import { User } from '@prisma/client';
import { UnauthorizedMvcExceptionFilter } from '@/common/filters/unauthorized-mvc-exception.filter';
import { LocalAuth } from '@/auth/local/local-auth.decorator';
import { AuthService } from '@/auth/auth.service';
import { SignupUserDto } from '@/auth/dtos/signup-user.dto';
import { UsersService } from '@/users/users.service';
import {
	ApiOperation,
	ApiOkResponse,
	ApiBody,
	ApiConflictResponse,
	ApiBadRequestResponse,
	ApiTags,
} from '@nestjs/swagger';
import { LoginUserDto } from '@/auth/dtos/login-user.dto';
import { MissingOrIncorrectFieldsResponse } from '@/common/openapi/responses';
import { BadRequestMvcExceptionFilter } from '@/common/filters/bad-request-mvc-exception.filter';
import { SessionData } from 'express-session';
import { ConflictMvcExceptionFilter } from '@/common/filters/conflict-mvc-exception.filter';

@ApiTags('MVC - Auth')
@Controller('auth')
@UseFilters(UnauthorizedMvcExceptionFilter)
@UseFilters(BadRequestMvcExceptionFilter)
@UseFilters(ConflictMvcExceptionFilter)
export class AuthMvcController {
	constructor(
		private authService: AuthService,
		private usersService: UsersService,
	) {}

	@Get('login')
	@ApiOperation({
		summary: 'Render the login page',
		description: 'Render the login page.',
		operationId: 'renderLoginMvc',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/login')
	renderLoginMvc(@Session() session: SessionData) {
		return {
			signupWaitingApproval: session.signupWaitingApproval,
		};
	}

	@Post('login')
	@LocalAuth()
	@HttpCode(200)
	@ApiOperation({
		summary: 'Log in to Spot in with username and password',
		description:
			'Log in to Spot in with username and password. Save the JWT in Cookie and redirect to `/spots`.',
		operationId: 'loginMvc',
	})
	@ApiBody({
		description: "The user's credentials.",
		type: LoginUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully logged in.',
	})
	@ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
	async loginMvc(
		@AuthUser() user: User,
		@Res() res: Response,
		@Session() session: SessionData,
	) {
		const jwt = await this.authService.generateJwt(user);

		delete session.signupWaitingApproval;

		res.cookie('jwt', jwt.jwt, {
			httpOnly: true,
			sameSite: true,
			secure: true,
		});

		res.redirect('/spots');
	}

	@Get('signup')
	@ApiOperation({
		summary: 'Render the signup page',
		description: 'Render the signup page.',
		operationId: 'renderSignupMvc',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/signup')
	renderSignupMvc(@Session() session: SessionData) {
		const errors = session.errors;

		delete session.errors;

		return {
			errors,
		};
	}

	@Post('signup')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Sign up to Spot in',
		description: 'Sign up to Spot in.',
		operationId: 'signupMvc',
	})
	@ApiBody({
		description: "The user's details.",
		type: SignupUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully signed up.',
	})
	@ApiConflictResponse({
		description: 'Another user has the same username.',
	})
	@ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
	async signupMvc(
		@Body() signupUserDto: SignupUserDto,
		@Res() res: Response,
		@Session() session: SessionData,
	) {
		await this.usersService.createUser(signupUserDto);

		session.signupWaitingApproval = true;

		res.redirect('/auth/login');
	}

	@Get('profile')
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the profile page',
		description: 'Render the profile page.',
		operationId: 'renderProfileMvc',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/profile')
	renderProfileMvc(@AuthUser() user: User) {
		return {
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get('logout')
	@JwtAuth()
	@ApiOperation({
		summary: 'Log out from Spot in',
		description:
			'Log out from Spot in. Clear the JWT Cookie and redirect to `/`.',
		operationId: 'logoutMvc',
	})
	@ApiOkResponse({
		description: 'The user has been successfully logged in.',
	})
	logoutMvc(@Res() res: Response) {
		res.clearCookie('jwt');

		res.redirect('/');
	}
}
