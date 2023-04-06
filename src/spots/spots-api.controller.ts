import {
  Body,
  Controller,
  Delete,
  Get,
  HttpCode,
  Param,
  Patch,
  Post,
  Res,
} from '@nestjs/common';
import {
  ApiBadRequestResponse,
  ApiBody,
  ApiCreatedResponse,
  ApiOperation,
  ApiOkResponse,
  ApiTags,
  ApiParam,
  ApiNotFoundResponse,
  ApiNoContentResponse,
} from '@nestjs/swagger';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';
import { SpotsService } from './spots.service';
import { Response } from 'express';
import { MissingOrIncorrectFieldsResponse } from '../common/openapi/responses';
import { ReadSpotDto } from './dtos/read-spot.dto';
import { AuthUser } from '../auth/decorators/auth-user.decorator';
import { Spot, User } from '@prisma/client';
import { JwtAuth } from '../auth/jwt/jwt-auth.decorator';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Get the spots',
    description: 'Get the spots.',
    operationId: 'getSpotsApi',
  })
  @ApiOkResponse({
    description: 'Spots have been successfully retrieved.',
    type: [ReadSpotDto],
  })
  async getSpotsApi(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @Get(':id')
  @JwtAuth()
  @ApiOperation({
    summary: 'Get the specified spot',
    description: 'Get the specified spot.',
    operationId: 'getSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Spot has been successfully retrieved.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id, user);

    return new ReadSpotDto(spot);
  }

  @Post()
  @JwtAuth()
  @ApiOperation({
    summary: 'Create a new spot',
    description: 'Create a new spot.',
    operationId: 'createSpotApi',
  })
  @ApiBody({
    description: "The spot's details.",
    type: CreateSpotDto,
  })
  @ApiCreatedResponse({
    description: 'Spot has been successfully created.',
    type: ReadSpotDto,
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async createSpotApi(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
  ) {
    const newSpot = await this.spotsService.createSpot(createSpotDto, user);

    return new ReadSpotDto(newSpot);
  }

  @Patch(':id')
  @JwtAuth()
  @ApiOperation({
    summary: 'Update the specified spot',
    description: 'Update the specified spot.',
    operationId: 'updateSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiBody({
    description: "The spot's details.",
    type: UpdateSpotDto,
  })
  @ApiOkResponse({
    description: 'Spot has been successfully updated.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  @ApiBadRequestResponse(MissingOrIncorrectFieldsResponse)
  async updateSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    const updatedSpot = await this.spotsService.updateSpot(
      id,
      updateSpot,
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @Delete(':id')
  @JwtAuth()
  @HttpCode(204)
  @ApiOperation({
    summary: 'Delete the specified spot',
    description: 'Delete the specified spot.',
    operationId: 'deleteSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiNoContentResponse({
    description: 'Spot has been successfully deleted.',
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async deleteSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }

  @Get(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirection success.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(@Res() res: Response, @Param('id') id: string) {
    const spot = (await this.spotsService.getSpot(id)) as Spot;

    if (!spot.configured) {
      res.redirect(`/spots/${id}/edit`);
    }

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @Get('public')
  @ApiOperation({
    summary: 'Get the public spots',
    description: 'Get the public spots.',
    operationId: 'getPublicSpotsApi',
  })
  @ApiOkResponse({
    description: 'Spots have been successfully retrieved.',
    type: [ReadSpotDto],
  })
  async getPublicSpotsApi() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }
}
